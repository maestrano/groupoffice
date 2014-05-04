<?php

/**
 * Mno Organization Class
 */
class MnoSoaPerson extends MnoSoaBasePerson
{
    protected $_local_entity_name = "CONTACT";
    protected $_related_organization_class = "MnoSoaOrganization";
    
    protected function pushName() {
        $this->_name->familyName = $this->push_set_or_delete_value($this->_local_entity->last_name);
        $this->_name->givenNames = $this->push_set_or_delete_value($this->_local_entity->first_name);
    }
    
    protected function pullName() {
        $this->_local_entity->last_name = $this->pull_set_or_delete_value($this->_name->familyName);
        $this->_local_entity->first_name = $this->pull_set_or_delete_value($this->_name->givenNames);
    }
    
    protected function pushBirthDate() {
        $this->_birth_date = $this->push_set_or_delete_value($this->_local_entity->birthday);
    }
    
    protected function pullBirthDate() {
        $this->_local_entity->birthday = $this->pull_set_or_delete_value($this->_birth_date);
    }
    
    protected function pushGender() {
        $this->_gender = $this->push_set_or_delete_value($this->_local_entity->sex);
    }
    
    protected function pullGender() {
        $this->_local_entity->sex = $this->pull_set_or_delete_value($this->_gender);
    }
    
    protected function pushAddresses() {
        // POSTAL ADDRESS -> POSTAL ADDRESS
        $this->_address->work->postalAddress->streetAddress = trim($this->push_set_or_delete_value($this->_local_entity->address) . ' ' . $this->push_set_or_delete_value($this->_local_entity->address_no));
        $this->_address->work->postalAddress->locality = $this->push_set_or_delete_value($this->_local_entity->city);
        $this->_address->work->postalAddress->region = $this->push_set_or_delete_value($this->_local_entity->state);
        $this->_address->work->postalAddress->postalCode = $this->push_set_or_delete_value($this->_local_entity->zip);
        $this->_address->work->postalAddress->country = strtoupper($this->push_set_or_delete_value($this->_local_entity->country));
    }
    
    protected function pullAddresses() {
	// POSTAL ADDRESS -> POSTAL ADDRESS
        $postal_street_address = $this->pull_set_or_delete_value($this->_address->work->postalAddress->streetAddress);
        if (strlen($postal_street_address) > 98) {
            $ww = wordwrap($postal_street_address, 98, "\n", true);
            $pieces = explode(" ", $ww);
            $this->_local_entity->address = $pieces[0];
            $this->_local_entity->address_no = $pieces[1];
        } else {
            $this->_local_entity->address = $postal_street_address;
            $this->_local_entity->address_no = "";
        }
        
        $this->_local_entity->city = $this->pull_set_or_delete_value($this->_address->work->postalAddress->locality);
        $this->_local_entity->state = $this->pull_set_or_delete_value($this->_address->work->postalAddress->region);
        $this->_local_entity->zip = $this->pull_set_or_delete_value($this->_address->work->postalAddress->postalCode);
        $this->_local_entity->country = $this->pull_set_or_delete_value($this->_address->work->postalAddress->country);
    }
    
    protected function pushEmails() {
        $this->_email->emailAddress = $this->push_set_or_delete_value($this->_local_entity->email);
        $this->_email->emailAddress2 = $this->push_set_or_delete_value($this->_local_entity->email2);
        $this->_email->emailAddress3 = $this->push_set_or_delete_value($this->_local_entity->email3);
    }
    
    protected function pullEmails() {
        $this->_local_entity->email = $this->pull_set_or_delete_value($this->_email->emailAddress);
        $this->_local_entity->email2 = $this->pull_set_or_delete_value($this->_email->emailAddress2);
        $this->_local_entity->email3 = $this->pull_set_or_delete_value($this->_email->emailAddress3);
    }
    
    
    protected function pushTelephones() {
        $this->_telephone->home->voice = $this->push_set_or_delete_value($this->_local_entity->home_phone);
        $this->_telephone->work->voice = $this->push_set_or_delete_value($this->_local_entity->work_phone);
        $this->_telephone->home->mobile = $this->push_set_or_delete_value($this->_local_entity->cellular);
        $this->_telephone->home->mobile2 = $this->push_set_or_delete_value($this->_local_entity->cellular2);
        $this->_telephone->home->fax = $this->push_set_or_delete_value($this->_local_entity->fax);
        $this->_telephone->work->fax = $this->push_set_or_delete_value($this->_local_entity->work_fax);
    }
    
    protected function pullTelephones() {
        $this->_local_entity->home_phone = $this->pull_set_or_delete_value($this->_telephone->home->voice);
        $this->_local_entity->work_phone = $this->pull_set_or_delete_value($this->_telephone->work->voice);
        $this->_local_entity->cellular = $this->pull_set_or_delete_value($this->_telephone->home->mobile);
        $this->_local_entity->cellular2 = $this->pull_set_or_delete_value($this->_telephone->home->mobile2);
        $this->_local_entity->fax = $this->pull_set_or_delete_value($this->_telephone->home->fax);
        $this->_local_entity->work_fax = $this->pull_set_or_delete_value($this->_telephone->work->fax);
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
            $this->_local_entity->go_user_id = 0;
            $this->_local_entity->addressbook_id=999;
        }
        $this->_local_entity->push_to_maestrano = false;
        $this->_local_entity->save(true);
    }
    
    public function getLocalEntityIdentifier() 
    {
        return $this->_local_entity->id;
    }
    
    public function getLocalEntityByLocalIdentifier($local_id)
    {
        return GO_Addressbook_Model_Contact::model()->findSingleByAttribute('id', $local_id);
    }
    
    public function createLocalEntity()
    {
        return new GO_Addressbook_Model_Contact();
    }
    
    public function getLocalOrganizationIdentifier()
    {
        return $this->_local_entity->company_id;
    }
    
    protected function setLocalOrganizationIdentifier($local_org_id)
    {
        $this->_local_entity->company_id = $local_org_id;
    }
}

?>