<?php
/**
 * Created by PhpStorm.
 * User: offic
 * Date: 10.06.2018
 * Time: 10:14
 */

class WunderlistConnection
{

    protected $user;
    protected $access_token;
    protected $hz_list;
    protected $wunderlist_id;





    const CLIENT_ID = "b881194992d82b909a32";
    const CLIENT_SECRET = "556d6699a365d889181292905a9f4a8c258920f395979d4312c71bdc6bf9";
    const CLIENT_ACCESS_TOKEN = "8e777b002240b5cb9402cf5c52c1a71502da00dbafae7dc1b6e3dc08a0fd";


    const WL_ACCESS_TOKEN_URL = "https://www.wunderlist.com/oauth/access_token";


    const WL_GET_USER = "https://a.wunderlist.com/api/v1/user";


    const WL_GET_FOLDER = "https://a.wunderlist.com/api/v1/folders";


    const WL_GET_LISTS = "https://a.wunderlist.com/api/v1/lists";
    const WL_GET_TASKS = "https://a.wunderlist.com/api/v1/tasks";



    function __construct($hz_list = null)
    {

        global $wpdb;

        $this->hz_list = $hz_list;

        $this->user = get_current_user_id();

        $this->get_token();

        if($hz_list != null)
        $this->wunderlist_id = $wpdb->get_var('SELECT wunderlist_id FROM ' . SHOPPING_LIST_TABLE . ' WHERE ID = ' . $this->hz_list);


    }



    function insert_list(){


        global $wpdb;
        $title = $wpdb->get_var('SELECT list_name FROM ' . SHOPPING_LIST_TABLE . ' WHERE ID = ' . $this->hz_list);

        $title = 'HERDZEIT - ' . $title;
        $create = $this->wunderlist_post(self::WL_GET_LISTS, ['title' => $title]);

        global $wpdb;
        $wpdb->update(
          SHOPPING_LIST_TABLE,
          [
              'wunderlist_id' => $create->id
          ],
            ['ID' => $this->hz_list]
        );
        return $create->id;
    }




    function wl_list_exists(){


        global $wpdb;

        if(empty($this->wunderlist_id)) return false;
        $exists = $this->wunderlist_get( 'https://a.wunderlist.com/api/v1/lists/' . $this->wunderlist_id);


        if(property_exists($exists, 'error')){

            $wpdb->delete(
                'wunderlist_id_map',
                [
                    'wunderlist_id' => $this->wunderlist_id
                ]
            );
            return false;
        }else{

            $list_name = $wpdb->get_var("SELECT list_name FROM  reciepe_shopping_lists WHERE ID = " . $this->hz_list);
            $update = $this->wunderlist_patch(self::WL_GET_LISTS . '/' . $exists->id, ['title' => 'HERDZEIT - ' . $list_name, 'revision' => $exists->revision]);
        }
        return true;

    }





    function get_all_items(){

        $tasks = $this->wunderlist_get(self::WL_GET_TASKS . '?list_id=' . $this->wunderlist_id);
        $tasks_completed = $this->wunderlist_get(self::WL_GET_TASKS . '?list_id=' . $this->wunderlist_id . '&completed=true');
        return array_merge($tasks, $tasks_completed);

    }


    function check_if_list_exists_or_insert(){

            if(!$this->wl_list_exists()){
                $this->wunderlist_id = $this->insert_list();
            }
            return [
                'wlist' => $this->wunderlist_id,
                'tasks' => $this->get_all_items()
            ];
    }






    function add_tasks( $tasks ){


      $this->clear_list();

      foreach($tasks as $task){


              $items[] = $this->insert_task($task);

      }
      return $items;

    }


    function task_exists($task_id){

        echo $task_id;

        if(!$task_id) return false;
        $exists = $this->wunderlist_get('https://a.wunderlist.com/api/v1/tasks/' . $task_id);

        return !property_exists( $exists, 'error');


    }


    function update_single_item($id, $completed = null){


        $item = $this->wunderlist_get(self::WL_GET_TASKS . '/' . $id);

        if($completed = null)  $completed =  !$item->completed;

        $revision = $item->revision;
        $this->wunderlist_patch('https://a.wunderlist.com/api/v1/tasks/' . $id,
            ['completed' => $completed, 'revision' => $revision]);

    }




    function clear_list(){


        $items = $this->get_all_items();

        foreach($items as $item){
            $rev = $item->revision;
            $delte = $this->wunderlist_del('https://a.wunderlist.com/api/v1/tasks/'.$item->id.'?revision=' . $rev, ['revision' => $rev ] );
            global $wpdb;
            $wpdb->delete('wunderlist_id_map', ['wunderlist_task' => $item->id]);

        }

    }




    function insert_task($task){

        $text = trim($task['text']);
        $text = preg_replace('/\s+/', ' ', $text);


        $insert = $this->wunderlist_post(self::WL_GET_TASKS, [
            'list_id' => (int) $this->wunderlist_id,
            'title'   => $text,
            'completed' => $task['checked'] == 'true' ? true : false
        ]);
        global $wpdb;
        $wpdb->insert(
            'wunderlist_id_map',
            [
                'list_id' => $this->hz_list,
                'ingredient_id' => $task['herdzeit_id'],
                'wunderlist_id'  => $this->wunderlist_id,
                'wunderlist_task' => $insert->id,
            ],
            [
                '%d',
                '%s',
                '%d',
                '%d'

            ]
        );

        return ['wunderlist_id' => $insert->id, 'hzerdzeit_id' => $task['herdzeit_id']];
    }


    function test_access_token()
    {

        $token = get_user_meta(get_current_user_id(), 'wunderlist_token', true);

        if ($token) {
            $this->access_token = $token;
            $response = $this->wunderlist_get(self::WL_GET_USER);

            if($response->error){
                return [
                    'connected' => false,
                    'redirect'  => $this->connect_url(home_url('wunderlist'))
                ];
            }else{
                return [
                    'connected' => true,
                    'redirect'  => 'https://www.wunderlist.com/#/lists/inbox'
                ];
            }

        } else {
            return [
                'connected' => false,
                'redirect'  => $this->connect_url(home_url('wunderlist'))
            ];
        }
    }
    function update_token($token)
    {
        update_user_meta($this->user, 'wundelist_token', $token);
        $this->access_token = $token;
    }
    function get_token()
    {
        $this->access_token = get_user_meta($this->user, 'wunderlist_token', true);
    }
    function connect_url($redirect)
    {
        return 'https://www.wunderlist.com/oauth/authorize?client_id=' . self::CLIENT_ID . '&redirect_uri=' . $redirect . '&state=' . wp_create_nonce('wunderlist-' . $this->user);
    }
    function wunderlist_post($url, $post)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-Access-Token: {$this->access_token}",
            "X-Client-ID: " . self::CLIENT_ID,
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);

        return json_decode($result);

    }
    function wunderlist_patch($url, $post)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-Access-Token: {$this->access_token}",
            "X-Client-ID: " . self::CLIENT_ID,
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);

        return json_decode($result);

    }
    function wunderlist_get($url)
    {


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-Access-Token: " . $this->access_token,
            "X-Client-ID: " . self::CLIENT_ID,
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        return json_decode( $result );
    }
    function wunderlist_del($url, $post)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "X-Access-Token: {$this->access_token}",
            "X-Client-ID: " . self::CLIENT_ID,
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"revision": "2"}');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);

        return json_decode($result);

    }
    function get_access_token()
    {

        $code = $_REQUEST['code'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET, 'code' => $code]));
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, 'https://www.wunderlist.com/oauth/access_token');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'APIKEY: ' .self::CLIENT_ACCESS_TOKEN,
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        if($info['http_code'] == 200){
            $result = json_decode($result);
            $this->access_token = $result->access_token;
            return update_user_meta(get_current_user_id(), 'wunderlist_token', $result->access_token);
        }
        return false;
    }

}