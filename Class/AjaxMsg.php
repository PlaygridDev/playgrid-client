<?php

if (! defined ( 'ROOT_DIR' )){ exit ( "Error, wrong way to file.<a href=\"/\">Go to main</a>.");}

class AjaxMsg
{

    private array $response;

    public function __construct()
    {
    }

    /**
     * @todo eval_js необходимо убрать, весь js код должен хранится и вызываться в фронтенд сайде
     */
    public function eval_js($js)
    {
        if(!empty($js)) {
            if(!empty($this->response["eval"])) {
                $this->response["eval"] .= $js;
            } else {
                $this->response["eval"] = $js;
            }
        }

        return $this;
    }

    public function location(string $url = '/', int $timeout = 3000)
    {

        if(!empty($url)) {
            $this->response["location"] = set_url($url, true);
            $this->response["time_sleep"] = $timeout;
        }

        return $this;

    }

    public function text(string $title, string $text)
    {
        $this->response["text"] = $text;
        $this->response["title"] = $title;
        return $this;
    }

    public function input_error($input_error = null)
    {
        if(!empty($input_error) AND (is_array($input_error) OR is_object($input_error))){
            foreach ((array)$input_error as $name => $text){

                if(strripos((string)$name, '.') !== false){
                    $temp = explode('.' , $name);
                    $name = '';
                    foreach ($temp as $segment){
                        if(empty($name))
                            $name .= $segment;
                        else
                            $name .= "[".$segment."]";
                    }
                }
                $this->response["input"][(string)$name] = (string)$text;
            }
        }

        return $this;
    }

    public function post($data)
    {
        $this->response["post"] = $data;
        return $this;
    }

    public function variables($data)
    {
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
        }

        $this->response = array_merge($this->response, $data);
        return $this;
    }

    public function notify(string $message, $url = null, $icon = null , $status = null , $time_show = 2000)
    {

        if(!empty($message)) {
            $this->response["text"] = $message;

            if($icon != null) {
                $this->response["icon"] = $icon;
            }

            if($url != null) {
                $this->response["location"] = set_url($url);
                $this->response["time_sleep"] = $time_show;
            }

            if($status != null) {
                $this->response["status"] = $status;
            }

            if($time_show != null) {
                $this->response["time_show"] = $time_show;
            }

        }

        return $this;

    }

    public function set_select($select_el, $select_set){
        $this->response["select_el"] = $select_el;
        $this->response["select_set"] = $select_set;
        return $this;
    }

    public function popup_close($id = "modal-ajax"){
        if(!empty($id)){
            if(!empty($this->response["eval"]))
                $this->response["eval"] .= "jQuery('#{$id}').modal('hide');";
            else
                $this->response["eval"] = "jQuery('#{$id}').modal('hide');";
        }
        return $this;
    }
    /*
     * modal-sm
     * modal-lg
     * modal-xl
     */
    public function popup($title, $content_html, $footer = '', $size = ''){
        $this->response["title"] = $title;
        $this->response["content"] = $content_html;
        $this->response["footer"] = $footer;
        $this->response["size"] = $size;

        $this->response["popup"] = true;
        return $this;
    }

    public function html(string $html, string $html_div = '') {

        if(!empty($html)) {
            $this->response["html"] = $html;
            if($html_div != null) {
                $this->response["html_div"] = $html_div;
            }
        }

        return $this;

    }

    public function setLocalStorage(string $key, string $value)
    {

        if(empty($this->response['localStorage'])) {
            $this->response['localStorage'] = [
                'items' => [],
            ];
        }

        array_push($this->response['localStorage']['items'], [
            'key' => $key,
            'value' => $value
        ]);

        return $this;

    }

    public function callback($callback_name , $data = null){
        if(!empty($callback_name)){
            $this->response["callback"] = $callback_name;

            if($data != null)
                $this->response["data"] = $data;
        }
        return json_encode($this->response);
    }

    public function success(){
        $this->response["status"] = 'success';
        return json_encode($this->response);
    }

    public function warning(){
        $this->response["status"] = 'warning';
        return json_encode($this->response);
    }

    public function info(){
        $this->response["status"] = 'info';
        return json_encode($this->response);
    }

    public function danger(){
        $this->response["status"] = 'danger';
        return json_encode($this->response);
    }
    public function error(){
        $this->response["status"] = 'error';
        return json_encode($this->response);
    }

    public function send(){
        return json_encode($this->response);
    }

}