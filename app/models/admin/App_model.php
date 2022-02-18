<?php

defined('BASEPATH') or exit('No direct script access allowed');

class App_model extends CI_Model
{
    private $Settings;

    public function __construct()
    {
        parent::__construct();
        $this->Settings = $this->getSettings();
        $this->load->config('rest');
    }

    protected function getSettings()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addBanner($data)
    {
        if ($this->db->insert('app_banners', $data)) {
            return true;
        }
        return false;
    }

    public function updateBanner($id, $data = [])
    {
        if ($this->db->update('app_banners', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteBanner($id)
    {
        if ($this->db->delete('app_banners', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getBannerByCode($code)
    {
        $q = $this->db->get_where('app_banners', ['code' => $code], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getBannerByID($id)
    {
        $q = $this->db->get_where('app_banners', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }





























    public function addNotification($data)
    {
        if ($this->db->insert('app_notifications', $data)) {
            return true;
        }
        return false;
    }

    public function updateNotification($id, $data = [])
    {
        if ($this->db->update('app_notifications', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function deleteNotification($id)
    {
        if ($this->db->delete('app_notifications', ['id' => $id])) {
            return true;
        }
        return false;
    }


    public function getNotificationByID($id)
    {
        $q = $this->db->get_where('app_notifications', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }




}
