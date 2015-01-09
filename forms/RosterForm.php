<?php

// Form used by our roster page
use Aura\Input\Builder;
use Aura\Input\Filter;
use Aura\Input\Form;

class RosterForm extends Form
{
    public function init()
    {
        // Define all our fields
        $this->setField('get_team', 'integer')
            ->setAttribs(['value' => 1]);
        $this->setField('id', 'integer');
        $this->setField('tig_name', 'text');
        $this->setField('item_type', 'integer');
        $this->setField('comments', 'text');
        $this->setField('status', 'integer');
        $this->setField('delete', 'boolean');
        $this->setField('release', 'boolean');
        
    }
}
