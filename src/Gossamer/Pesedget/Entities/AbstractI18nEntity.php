<?php

namespace Gossamer\Pesedget\Entities;

use Gossamer\Pesedget\Entities\AbstractEntity;

abstract class AbstractI18nEntity extends AbstractEntity
{

    protected $isI18n = false;

    private $i18nValues = array();

    protected $i18nPrimaryKeys = array();

    public function __construct() {
        parent::__construct();
        $this->i18nPrimaryKeys = array($this->getI18nIdentifier(), 'locale');
    }
    public function getI18nPrimaryKeys(){
        return $this->i18nPrimaryKeys;
    }
    public function getI18nIdentifier(){
        return $this->getTableName() . '_id';
    }

    public function getI18nTablename(){
        return $this->getTableName() . 'I18n';
    }

    public function populateI18nValues($values = array()) {
        $this->i18nValues = $values;
    }

    public function getI18nValues($locale = null) {
        if(is_null($locale)) {
            return $this->i18nValues;
        }elseif(array_key_exists($locale, $this->i18nValues)) {
            return $this->i18nValues[$locale];
        }

        throw new LocaleNotSupportedException('Unable to locate ' . $locale . 'for class ' . get_class($this));
    }

    public function addLocale($locale, $localizedArray = array()) {
        $this->i18nValues[$locale] = $localizedArray;
    }


    public function populate($params = array(), $i18nParams = array()){
        parent::populate($params);

        foreach ($i18nParams as $key => $value) {
            if(is_int($key)){
                continue;
            }

            $this->i18nValues[$key] = $value;
        }
    }
}

