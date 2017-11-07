<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2017 OA Wu Design
 * @license     http://creativecommons.org/licenses/by-nc/2.0/tw/
 */

class LogFile extends OaModel {

  static $table_name = 'log_files';

  static $has_one = array (
  );

  static $has_many = array (
  );

  static $belongs_to = array (
  );

  public function __construct ($attributes = array (), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
    parent::__construct ($attributes, $guard_attributes, $instantiating_via_find, $new_record);

    OrmFileUploader::bind ('file', 'LogFileFileFileUploader');
  }
  public function putFile2S3 () {
    if (!(isset ($this->id) && isset ($this->file) && !((string)$this->file) && isset ($this->name) && isset ($this->message_id) && $this->message_id)) return false;
    
    $this->CI->load->library ('OALineBot');

    if (!$oaLineBot = OALineBot::create ())
      return false;

    $response = $oaLineBot->bot ()->getMessageContent ($this->message_id);
    if (!$response->isSucceeded ())
      return false;

    $ext = ($ext = pathinfo ($this->name, PATHINFO_EXTENSION)) ? '.' . $ext : '';
    if (!$ext) $ext = ($contentType = $response->getHeader ('Content-Type')) ? contentType2ext ($contentType) : '';
    $path = FCPATH . 'temp' . DIRECTORY_SEPARATOR . uniqid (rand () . '_') . $ext;
    
    if (!write_file ($path, $response->getRawBody ()))
      return false;

    return $this->file->put ($path);
  }
}