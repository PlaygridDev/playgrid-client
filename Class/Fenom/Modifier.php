<?php
/*
 * This file is part of Fenom.
 *
 * (c) 2013 Ivan Shalganov
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */
namespace Fenom;

/**
 * Collection of modifiers
 * @author     Ivan Shalganov <a.cobest@gmail.com>
 */
class Modifier
{

    /**
     * Date format
     *
     * @param string|int $date
     * @param string $format
     * @return string
     */
    public static function dateFormat($date, $format = "%b %e, %Y")
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
            if (!$date) {
                $date = time();
            }
        }
        // strftime() is deprecated since PHP 8.1 and removed in PHP 9
        return self::strftimeFormat($format, (int) $date);
    }

    /**
     * Renders a strftime()-style format through date().
     *
     * The application never calls setlocale(), so strftime() ran under the "C" locale and date() gives the same
     * English names. The conversion specifiers and the glibc flags (%-d, %_H, %0e, %^b, %#Z, %Ey, %Od) are
     * rendered byte by byte so that multibyte literals in the format survive; an unknown specifier is left in
     * place as strftime() did.
     *
     * @param string $format strftime() format
     * @param int $time unix timestamp
     * @return string
     */
    public static function strftimeFormat($format, $time)
    {
        $out = '';
        $len = strlen($format);
        for ($i = 0; $i < $len; $i++) {
            $c = $format[$i];
            if ($c !== '%' || $i + 1 >= $len) {
                $out .= $c;
                continue;
            }
            $spec = $format[++$i];
            $flag = '';
            if (strpos('-_0^#', $spec) !== false && $i + 1 < $len) {
                $flag = $spec;
                $spec = $format[++$i];
            }
            if (($spec === 'E' || $spec === 'O') && $i + 1 < $len) {
                // locale alternative representations: identical to the plain ones in the "C" locale
                $spec = $format[++$i];
            }
            $piece = self::strftimeSpec($spec, $time);
            if ($piece === null) {
                $out .= '%' . $flag . $spec;
                continue;
            }
            switch ($flag) {
                case '-':
                    $trimmed = ltrim($piece, '0 ');
                    $piece = $trimmed === '' ? substr($piece, -1) : $trimmed;
                    break;
                case '_':
                    $trimmed = ltrim($piece, '0 ');
                    $piece = str_pad($trimmed === '' ? substr($piece, -1) : $trimmed, strlen($piece), ' ', STR_PAD_LEFT);
                    break;
                case '0':
                    $piece = str_pad(ltrim($piece, ' '), strlen($piece), '0', STR_PAD_LEFT);
                    break;
                case '^':
                    $piece = strtoupper($piece);
                    break;
                case '#':
                    $piece = ($spec === 'p' || $spec === 'Z') ? strtolower($piece) : strtoupper($piece);
                    break;
            }
            $out .= $piece;
        }
        return $out;
    }

    /**
     * One strftime() conversion specifier rendered through date(), null when the specifier is unknown.
     *
     * @param string $spec specifier letter
     * @param int $time unix timestamp
     * @return string|null
     */
    private static function strftimeSpec($spec, $time)
    {
        switch ($spec) {
            // day
            case 'a': return date('D', $time);
            case 'A': return date('l', $time);
            case 'd': return date('d', $time);
            case 'e': return sprintf('%2d', date('j', $time));
            case 'j': return sprintf('%03d', date('z', $time) + 1);
            case 'u': return date('N', $time);
            case 'w': return date('w', $time);
            // week
            case 'U': return sprintf('%02d', floor((date('z', $time) + 7 - date('w', $time)) / 7));
            case 'W': return sprintf('%02d', floor((date('z', $time) + 7 - (date('w', $time) + 6) % 7) / 7));
            case 'V': return date('W', $time);
            // month
            case 'b':
            case 'h': return date('M', $time);
            case 'B': return date('F', $time);
            case 'm': return date('m', $time);
            // year
            case 'C': return sprintf('%02d', floor(date('Y', $time) / 100));
            case 'g': return sprintf('%02d', date('o', $time) % 100);
            case 'G': return date('o', $time);
            case 'y': return date('y', $time);
            case 'Y': return date('Y', $time);
            // time
            case 'H': return date('H', $time);
            case 'k': return sprintf('%2d', date('G', $time));
            case 'I': return date('h', $time);
            case 'l': return sprintf('%2d', date('g', $time));
            case 'M': return date('i', $time);
            case 'p': return date('A', $time);
            case 'P': return date('a', $time);
            case 'r': return date('h:i:s A', $time);
            case 'R': return date('H:i', $time);
            case 'S': return date('s', $time);
            case 'T':
            case 'X': return date('H:i:s', $time);
            case 'z': return date('O', $time);
            case 'Z': return date('T', $time);
            // stamps
            case 'c': return self::strftimeFormat('%a %b %e %H:%M:%S %Y', $time);
            case 'D':
            case 'x': return date('m/d/y', $time);
            case 'F': return date('Y-m-d', $time);
            case 's': return (string) $time;
            // literals
            case 'n': return "\n";
            case 't': return "\t";
            case '%': return '%';
        }
        return null;
    }

    /**
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function date($date, $format = "Y m d")
    {
        if (!is_numeric($date)) {
            $date = strtotime($date);
            if (!$date) {
                $date = time();
            }
        }
        return date($format, $date);
    }

    /**
     * Escape string
     *
     * @param string $text
     * @param string $type
     * @param string $charset
     * @return string
     */
    public static function escape($text, $type = 'html', $charset = null)
    {
        switch (strtolower($type)) {
            case "url":
                return urlencode($text);
            case "html";
                return htmlspecialchars($text, ENT_COMPAT, $charset ? $charset : \Fenom::$charset);
            case "js":
                return json_encode($text, 64 | 256); // JSON_UNESCAPED_SLASHES = 64, JSON_UNESCAPED_UNICODE = 256
            default:
                return $text;
        }
    }

    /**
     * Unescape escaped string
     *
     * @param string $text
     * @param string $type
     * @return string
     */
    public static function unescape($text, $type = 'html')
    {
        switch (strtolower($type)) {
            case "url":
                return urldecode($text);
            case "html";
                return htmlspecialchars_decode($text, ENT_COMPAT | ENT_HTML401);
            default:
                return $text;
        }
    }

    /**
     * Crop string to specific length (support unicode)
     *
     * @param string $string text witch will be truncate
     * @param int $length maximum symbols of result string
     * @param string $etc place holder truncated symbols
     * @param bool $by_words
     * @param bool $middle
     * @return string
     */
    public static function truncate($string, $length = 80, $etc = '...', $by_words = false, $middle = false)
    {
        if ($middle) {
            if (preg_match('#^(.{' . $length . '}).*?(.{' . $length . '})?$#usS', $string, $match)) {
                if (count($match) == 3) {
                    if ($by_words) {
                        return preg_replace('#\s\S*$#usS', "", $match[1]) .
                               $etc .
                               preg_replace('#\S*\s#usS', "", $match[2]);
                    } else {
                        return $match[1] . $etc . $match[2];
                    }
                }
            }
        } else {
            if (preg_match('#^(.{' . $length . '})#usS', $string, $match)) {
                if ($by_words) {
                    return preg_replace('#\s\S*$#usS', "", $match[1]) . $etc;
                } else {
                    return $match[1] . $etc;
                }
            }
        }
        return $string;
    }

    /**
     * Strip spaces symbols on edge of string end multiple spaces in the string
     *
     * @param string $str
     * @param bool $to_line strip line ends
     * @return string
     */
    public static function strip($str, $to_line = false)
    {
        $str = trim($str);
        if ($to_line) {
            return preg_replace('#\s+#ms', ' ', $str);
        } else {
            return preg_replace('#[ \t]{2,}#', ' ', $str);
        }
    }

    /**
     * Return length of UTF8 string, array, countable object
     * @param mixed $item
     * @return int
     */
    public static function length($item)
    {
        if (is_string($item)) {
            return strlen(preg_replace('#[\x00-\x7F]|[\x80-\xDF][\x00-\xBF]|[\xE0-\xEF][\x00-\xBF]{2}#s', ' ', $item));
        } elseif (is_array($item)) {
            return count($item);
        } elseif ($item instanceof \Countable) {
            return $item->count();
        } else {
            return 0;
        }
    }

    /**
     *
     * @param mixed $value
     * @param mixed $haystack
     * @return bool
     */
    public static function in($value, $haystack)
    {
        if(is_scalar($value)) {
            if (is_array($haystack)) {
                return in_array($value, $haystack) || array_key_exists($value, $haystack);
            } elseif (is_string($haystack)) {
                return strpos($haystack, $value) !== false;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isIterable($value)
    {
        return is_array($value) || ($value instanceof \Iterator);
    }

    /**
     * Replace all occurrences of the search string with the replacement string
     * @param string $value The string being searched and replaced on, otherwise known as the haystack.
     * @param string $search The value being searched for, otherwise known as the needle.
     * @param string $replace The replacement value that replaces found search
     * @return mixed
     */
    public static function replace($value, $search, $replace)
    {
        return str_replace($search, $replace, $value);
    }

    /**
     * @param string $value
     * @param string $pattern
     * @param string $replacement
     * @return mixed
     */
    public static function ereplace($value, $pattern, $replacement)
    {
        return preg_replace($pattern, $replacement, $value);
    }

    /**
     * @param string $string
     * @param string $pattern
     * @return bool
     */
    public static function match($string, $pattern)
    {
        return fnmatch($pattern, $string);
    }

    /**
     * @param string $string
     * @param string $pattern
     * @return int
     */
    public static function ematch($string, $pattern)
    {
        return preg_match($pattern, $string);
    }

    /**
     * @param string $value
     * @param string $delimiter
     * @return array
     */
    public static function split($value, $delimiter = ",")
    {
        if(is_string($value)) {
            return explode($delimiter, $value);
        } elseif(is_array($value)) {
            return $value;
        } else {
            return array();
        }
    }

    /**
     * @param $value
     * @param string $pattern
     * @return array
     */
    public static function esplit($value, $pattern = '/,\s*/S')
    {
        if(is_string($value)) {
            return preg_split($pattern, $value);
        } elseif(is_array($value)) {
            return $value;
        } else {
            return array();
        }
    }

    /**
     * @param $value
     * @param string $glue
     * @return string
     */
    public static function join($value, $glue = ",")
    {
        if(is_array($value)) {
            return implode($glue, $value);
        } elseif(is_string($value)) {
            return $value;
        } else {
            return "";
        }
    }

    /**
     * @param string|int $from
     * @param string|int $to
     * @param int $step
     * @return RangeIterator
     */
    public static function range($from, $to, $step = 1) {
        if($from instanceof RangeIterator) {
            return $from->setStep($to);
        } else {
            return new RangeIterator($from, $to, $step);
        }
    }
}
