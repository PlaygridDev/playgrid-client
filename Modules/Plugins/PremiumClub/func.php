<?php

namespace PremiumClub;

class func
{

    public $this_main;

    public function __construct($this_main)
    {
        $this->this_main = $this_main;
    }

    public static function isActive(): bool
    {
        return get_instance()->check_plugin('premium_club') && !empty(get_instance()->premium_club['status']) && self::data() !== null;
    }

    public static function getDiscount(string $type): float
    {
        $percent = 0.0;

        foreach (self::getStatuses() as $status) {
            $percent += $status['discounts'][$type] ?? 0.0;
        }

        return round($percent, 2);
    }

    public static function getStatuses(): array
    {
        if (!self::isActive()) {
            return [];
        }

        $data         = self::data();
        $groups       = self::groups();
        $bonusBalance = get_instance()->check_plugin('bonus_balance') && !empty(get_instance()->config['bonus_balance']['status']);
        $statuses     = [];

        foreach (is_array($data['statuses'] ?? null) ? $data['statuses'] : [] as $status) {
            if (!is_array($status)) {
                continue;
            }

            $group    = $groups[(int) ($status['group_id'] ?? 0)] ?? null;
            $levels   = $group && is_array($group['levels'] ?? null) ? array_values(array_filter($group['levels'], fn($level) => is_array($level) && isset($level['id']))) : [];
            $progress = (float) ($status['progress'] ?? 0);
            $index    = false;

            foreach ($levels as $position => $candidate) {
                if ($progress + 0.0005 < (float) ($candidate['threshold'] ?? 0)) {
                    break;
                }
                $index = $position;
            }

            if ($index === false) {
                continue;
            }

            $level      = $levels[$index];
            $nextLevel  = $levels[$index + 1] ?? null;
            $nextGroup  = $groups[(int) ($group['next_id'] ?? 0)] ?? null;
            $completion = is_numeric($group['completion'] ?? null) ? (float) $group['completion'] : null;
            $bonus      = is_array($level['bonus'] ?? null) ? $level['bonus'] : [];

            if ($nextLevel) {
                $goal     = 'level';
                $boundary = (float) $nextLevel['threshold'];
            } elseif ($nextGroup && $completion !== null) {
                $goal     = 'transition';
                $boundary = $completion;
            } else {
                $goal     = 'max';
                $boundary = null;
            }

            $statuses[] = [
                'group_id'        => (int) $group['id'],
                'group_name'      => (string) $group['name'],
                'icon_url'        => !empty($group['icon_url']) ? (string) $group['icon_url'] : null,
                'level_name'      => (string) $level['name'],
                'progress'        => $progress,
                'goal'            => $goal,
                'next_group_name' => $goal === 'transition' ? (string) $nextGroup['name'] : null,
                'remaining'       => $boundary === null ? 0.0 : round(max(0.0, $boundary - $progress), 3),
                'percent'         => $boundary > 0 ? (int) min(100, max(0, round($progress / $boundary * 100))) : 100,
                'discounts'       => array_map(fn($percent) => is_numeric($percent) ? (float) $percent : null, is_array($level['discounts'] ?? null) ? $level['discounts'] : []),
                'bonus'           => [
                    'target'   => ($bonus['target'] ?? '') === 'bonus' && $bonusBalance ? 'bonus' : 'main',
                    'lifetime' => (int) ($bonus['lifetime'] ?? 0),
                    'ranges'   => self::bonusRanges($bonus),
                ],
            ];
        }

        return $statuses;
    }

    private static function bonusRanges(array $bonus): array
    {
        $ranges = is_array($bonus['ranges'] ?? null) ? array_filter($bonus['ranges'], 'is_array') : [];
        $result = [];

        foreach ($ranges as $range) {
            $end = is_numeric($range['end'] ?? null) ? (float) $range['end'] : null;

            $result[] = [
                'start'   => (float) ($range['start'] ?? 0),
                'end'     => $end,
                'percent' => (float) ($range['percent'] ?? 0),
                'open'    => $end === null,
            ];
        }

        return $result;
    }

    public function widget_status()
    {
        $statuses = self::getStatuses();

        if (empty($statuses)) {
            return '';
        }

        $lang = get_lang('premium_club.lang');

        $types = array();
        foreach (array('shop', 'service', 'game_valute', 'games') as $type) {
            $types[$type] = $lang['type_' . $type] ?? $type;
        }

        return get_instance()->fenom->fetch(
            get_tpl_file('widget_status.tpl', get_class($this->this_main)),
            array_merge(
                array(
                    'statuses' => $statuses,
                    'types' => $types,
                    'payment_system' => get_instance()->config['payment_system'],
                ),
                $lang
            )
        );
    }

    private static function groups(): array
    {
        $config   = get_instance()->premium_club;
        $serverId = ($config['progress_mode'] ?? '') === 'per_server' ? get_instance()->session->getSid() : 0;
        $groups   = [];

        foreach (is_array($config['groups'][$serverId] ?? null) ? $config['groups'][$serverId] : [] as $group) {
            if (is_array($group) && isset($group['id'])) {
                $groups[(int) $group['id']] = $group;
            }
        }

        return $groups;
    }

    private static function data(): ?array
    {
        $data = get_instance()->session->session['user_data']['premium_club'] ?? null;

        return is_array($data) ? $data : null;
    }

}
