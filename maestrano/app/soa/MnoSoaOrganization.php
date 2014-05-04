<?php

/**
 * Mno Organization Class
 */
class MnoSoaOrganization extends MnoSoaBaseOrganization
{
    protected $_local_entity_name = "COMPANY";
    
    protected function pushName() {
        $this->_name = $this->push_set_or_delete_value($this->_local_entity->name);
    }
    
    protected function pullName() {
        $this->_local_entity->name = $this->pull_set_or_delete_value($this->_name);
    }
    
    protected function pushIndustry() {
	// DO NOTHING
    }
    
    protected function pullIndustry() {
	// DO NOTHING
    }
    
    protected function pushAnnualRevenue() {
	// DO NOTHING
    }
    
    protected function pullAnnualRevenue() {
	// DO NOTHING
    }
    
    protected function pushCapital() {
        // DO NOTHING
    }
    
    protected function pullCapital() {
        // DO NOTHING
    }
    
    protected function pushNumberOfEmployees() {
	// DO NOTHING
    }
    
    protected function pullNumberOfEmployees() {
       // DO NOTHING
    }
    
    protected function pushAddresses() {
        // POSTAL ADDRESS -> POSTAL ADDRESS
        $this->_address->postalAddress->streetAddress = trim($this->push_set_or_delete_value($this->_local_entity->post_address) . ' ' . $this->push_set_or_delete_value($this->_local_entity->post_address_no));
        $this->_address->postalAddress->locality = $this->push_set_or_delete_value($this->_local_entity->post_city);
        $this->_address->postalAddress->region = $this->push_set_or_delete_value($this->_local_entity->post_state);
        $this->_address->postalAddress->postalCode = $this->push_set_or_delete_value($this->_local_entity->post_zip);
        $this->_address->postalAddress->country = strtoupper($this->push_set_or_delete_value($this->_local_entity->post_country));
        // STREET ADDRESS -> STREET ADDRESS
        $this->_address->streetAddress->streetAddress = trim($this->push_set_or_delete_value($this->_local_entity->address) . ' ' . $this->push_set_or_delete_value($this->_local_entity->address_no));
        $this->_address->streetAddress->locality = $this->push_set_or_delete_value($this->_local_entity->city);
        $this->_address->streetAddress->region = $this->push_set_or_delete_value($this->_local_entity->state);
        $this->_address->streetAddress->postalCode = $this->push_set_or_delete_value($this->_local_entity->zip);
        $this->_address->streetAddress->country = strtoupper($this->push_set_or_delete_value($this->_local_entity->country));
    }
    
    protected function pullAddresses() {
        // POSTAL ADDRESS -> POSTAL ADDRESS
        $postal_street_address = $this->pull_set_or_delete_value($this->_address->postalAddress->streetAddress);
        
        if (strlen($postal_street_address) > 98) {
            $ww = wordwrap($postal_street_address, 98, "\n", true);
            $pieces = explode(" ", $ww);
            $this->_local_entity->post_address = $pieces[0];
            $this->_local_entity->post_address_no = $pieces[1];
        } else {
            $this->_local_entity->post_address = $postal_street_address;
            $this->_local_entity->post_address_no = "";
        }
        
        $this->_local_entity->post_city = $this->pull_set_or_delete_value($this->_address->postalAddress->locality);
        $this->_local_entity->post_state = $this->pull_set_or_delete_value($this->_address->postalAddress->region);
        $this->_local_entity->post_zip = $this->pull_set_or_delete_value($this->_address->postalAddress->postalCode);
        $this->_local_entity->post_country = $this->pull_set_or_delete_value($this->_address->postalAddress->country);
        // STREET ADDRESS -> STREET ADDRESS
        $physical_street_address = $this->pull_set_or_delete_value($this->_address->streetAddress->streetAddress);
        
        if (strlen($physical_street_address) > 98) {
            $ww = wordwrap($physical_street_address, 98, "\n", true);
            $pieces = explode(" ", $ww);
            $this->_local_entity->address = $pieces[0];
            $this->_local_entity->address_no = $pieces[1];
        } else {
            $this->_local_entity->address = $physical_street_address;
            $this->_local_entity->address_no = "";
        }
        
        $this->_local_entity->city = $this->pull_set_or_delete_value($this->_address->streetAddress->locality);
        $this->_local_entity->state = $this->pull_set_or_delete_value($this->_address->streetAddress->region);
        $this->_local_entity->zip = $this->pull_set_or_delete_value($this->_address->streetAddress->postalCode);
        $this->_local_entity->country = $this->pull_set_or_delete_value($this->_address->streetAddress->country);
    }
    
    protected function pushEmails() {
        $this->_email->emailAddress = $this->push_set_or_delete_value($this->_local_entity->email);
        $this->_email->emailAddress2 = $this->push_set_or_delete_value($this->_local_entity->invoice_email);
    }
    
    protected function pullEmails() {
        $this->_local_entity->email = $this->pull_set_or_delete_value($this->_email->emailAddress);
        $this->_local_entity->invoice_email = $this->pull_set_or_delete_value($this->_email->emailAddress2);
    }
    
    protected function pushTelephones() {
        $this->_telephone->voice = $this->push_set_or_delete_value($this->_local_entity->phone);
        $this->_telephone->fax = $this->push_set_or_delete_value($this->_local_entity->fax);
    }
    
    protected function pullTelephones() {
        $this->_local_entity->phone = $this->pull_set_or_delete_value($this->_telephone->voice);
        $this->_local_entity->fax = $this->pull_set_or_delete_value($this->_telephone->fax);
    }
    
    protected function pushWebsites() {
        $this->_website->url = $this->push_set_or_delete_value($this->_local_entity->homepage);
    }
    
    protected function pullWebsites() {
        $this->_local_entity->homepage = $this->pull_set_or_delete_value($this->_website->url);
    }
    
    protected function pushEntity() {
        // DO NOTHING
    }
    
    protected function pullEntity() {
        // DO NOTHING
    }
        
    protected function saveLocalEntity($push_to_maestrano, $status) {
        if ($status == constant('MnoSoaBaseEntity::STATUS_NEW_ID')) {
            $this->_local_entity->user_id = 0;
            $this->_local_entity->muser_id = 0;
            $this->_local_entity->addressbook_id=999;
        }
        $this->_local_entity->push_to_maestrano = false;
        $this->_local_entity->save(true);
    }
    
    public function getLocalEntityIdentifier() {
        return $this->_local_entity->id;
    }
    
    public function getLocalEntityByLocalIdentifier($local_id) {
        return GO_Addressbook_Model_Company::model()->findSingleByAttribute('id', $local_id);
    }
    
    public function createLocalEntity() {
        return new GO_Addressbook_Model_Company();
    }
    
}

?>