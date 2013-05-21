<?php

class Cck_Magefinder_Model_Resource_Fulltext_Engine 
{

    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product') 
    {
        $data    = array();
        $storeId = (int)$storeId;
        $lang   = Mage::getStoreConfig('magefinder/advanced/language', $storeId);
        foreach ($entityIndexes as $entityId => $index) {
            $index['api_key']       = Mage::getStoreConfig('magefinder/general/access_key', $storeId);
            $index['store_id']      = (int)$storeId;
            $index['product_id']    = (int)$entityId;
            $data[] = array(
                'type'  => 'add',
                'id'    => Mage::helper('magefinder')->getCfId($entityId, $storeId),
                'lang'  => $lang,
                'version' => Mage::helper('magefinder')->getVersion(),
                'fields'  => $index
            );
        }

        if ($data) {
            $this->_getDocAdapter()->import($data);
        }

        return $this;
	}

	public function allowAdvancedIndex()
    {
		return false;
	}

    public function cleanIndex($storeId = null, $entityId = null, $entity = 'product')
    {
        if(is_null($entityId)) {
            $this->_getDocAdapter()->truncate($storeId);
        }
        else {
            $this->_getDocAdapter()->delete($storeId, $entityId);
        }
    }

    public function prepareEntityIndex($index, $separator = ' ')
    {
        return Mage::helper('magefinder')->prepareIndexdata($index, $separator);
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds();
    }
    
    protected function _getDocAdapter()
    {
        return Mage::getResourceSingleton('magefinder/cloudsearch');
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLeyeredNavigationAllowed()
    {
        return true;
    }

    public function test()
    {
        return true;
    }

    public function __call($method, $args)
    {
        Mage::log($method);
        Mage::log($args);
        return Mage::getResourceSingleton('catalogsearch/fulltext_engine')->$method($args);
    }
    
};