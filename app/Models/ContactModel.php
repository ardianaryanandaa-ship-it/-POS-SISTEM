<?php 

namespace App\Models;
use CodeIgniter\Model;

class ContactModel extends Model {
    protected $table = 'web_contact';
    protected $allowedFields = ['contact_key', 'contact_value'];

    public function getContactArray() {
        $data = $this->findAll();
        $result = [];
        foreach ($data as $row) {
            $result[$row['contact_key']] = $row['contact_value'];
        }
        return $result;
    }
}