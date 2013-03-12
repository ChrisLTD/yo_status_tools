<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
    'pi_name'       => 'YO Status Tools',
    'pi_version'        => '0.01',
    'pi_author'     => 'Chris Johnson',
    'pi_author_url'     => 'http://chrisltd.com/',
    'pi_description'    => 'Can change the status of a channel entry, delete old entries by status, or read a status',
    'pi_usage'      => yo_status_tools::usage()
);

class yo_status_tools {

    public function __construct()
    {
        $this->EE =& get_instance();
    }

    public function return_entry_status() 
    {
      $url_title = $this->EE->TMPL->fetch_param('url_title');
      $entry_id = $this->EE->TMPL->fetch_param('entry_id');
      if(!$url_title && !$entry_id)
        return '<p>Error: No "url_title" or "channel_name" parameter';

      $this->EE->db->select('url_title, entry_id, status')
                              ->limit(1);
      if($url_title)
        $this->EE->db->where('url_title', $url_title);
      elseif ($entry_id)
        $this->EE->db->where('entry_id', $entry_id);
      $query = $this->EE->db->get('channel_titles');
      $status_result = $query->row('status');

      return $status_result; 
    }

    public function change_entry_status()
    {
      $new_status = $this->EE->TMPL->fetch_param('new_status');
      $url_title = $this->EE->TMPL->fetch_param('url_title');
      $entry_id = $this->EE->TMPL->fetch_param('entry_id');

      if(!$new_status){
        return '<p>Error: No "new_status" specified</p>';
      } 
      $update_array = array( 'status' => $new_status );

      $this->EE->db->limit(1);
      if($url_title)
        $this->EE->db->where('url_title', $url_title);
      elseif ($entry_id) 
        $this->EE->db->where('entry_id', $entry_id);
      else
        return '<p>Error: No "entry_id" or "url_title" specified</p>';

      $this->EE->db->update('channel_titles', $update_array);

    }

    public function delete_old_entries() 
    {
      $output = "";
      $entry_id_array = array();
      $status = $this->EE->TMPL->fetch_param('status');
      $age_in_seconds = $this->EE->TMPL->fetch_param('age_in_seconds');
      $channel_name = $this->EE->TMPL->fetch_param('channel_name');
      if(!$status)
        return '<p>Error: No "status" parameter';
      if(!$age_in_seconds)
        return '<p>Error: No "age_in_seconds" parameter';
      if(!$channel_name)
        return '<p>Error: No "channel_name" parameter';

      $this->EE->db->select('channel_id, channel_name')
                              ->limit(1)
                              ->where('channel_name', $channel_name);
      $query = $this->EE->db->get('channels');
      $channel_id = $query->row('channel_id');

      // $output .= "Channel ID: " . $channel_id . "<br>";

      $oldest_entry_date = time() - $age_in_seconds;

      $this->EE->db->select('entry_id, status, entry_date, channel_id')
                              ->where('entry_date <', $oldest_entry_date)
                              ->where('channel_id', $channel_id)
                              ->where('status', $status);
      $query = $this->EE->db->get('channel_titles');
      $status_result = $query->row('status');

      foreach($query->result() as $row)
      {
        $entry_id_array[] = $row->entry_id;
      }
      if($entry_id_array[0])
        $this->EE->load->library('api');
        $this->EE->api->instantiate('channel_entries');
        $this->EE->api_channel_entries->delete_entry($entry_id_array);      

      // $output .= '<pre>' . print_r($entry_id_array, true) . '</pre>';

      return $output; 
    }


    public static function usage()
    {
        ob_start(); 
        ?>
            YO Status Tools will let you change the status of a channel entry, delete old entries by status, or read a status from a template.

            In the parameters for the tag you need to supply either a url_title or entry_id and a new_status.

            Examples:

            {exp:yo_status_tools:change_entry_status url_title="URL TITLE HERE" new_status="closed"}

            {exp:yo_status_tools:delete_old_entries channel_name="CHANNEL_NAME" age_in_seconds="2628000" status="closed"}
            
            {exp:yo_status_tools:read_entry_status url_title="URL_TITLE_HERE"}

        <?php
        $buffer = ob_get_contents();

        ob_end_clean(); 

        return $buffer;
    }

}
/* End of file pi.yo_status_tools.php */
/* Location: ./system/expressionengine/third_party/yo_status_tools/pi.yo_status_tools.php */