<?php
/**
 *
 * HospitalConfigForm.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */

/**
 * Description of HospitalConfigForm
 *
 * @package application.models
 */
class HospitalConfigForm extends CFormModel {

    protected $prefix = 'hie_service_';
    public $hospital_name;
    public $client_id;
    public $client_secret;
    public $base_url;
    public $files_url;
    public $hospital_code;

    /**
     *
     * @param string $scenario
     */
    public function __construct($scenario = '') {
        parent::__construct($scenario);

        $settings = array('hospital_name', 'client_id', 'client_secret', 'base_url', 'files_url','hospital_code');
        foreach ($settings as $v) {
            try {
                $this->$v = (string) Config::get($this->prefix . $v);
            } catch (CException $e) {
                // do nothing
            }
        }
    }

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('hospital_name, client_id, client_secret, base_url, files_url, hospital_code', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'hospital_name'=>'Hospital name',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret Key',
            'base_url' => 'HIE service base URL',
            'files_url' => 'HIE Files Access URL',
            'hospital_code' => 'Hospital Code'
        );
    }

    /**
     *
     */
    public function save() {
        return Config::saveConfigurations(array(
            $this->prefix.'hospital_name' => $this->hospital_name,
            $this->prefix.'client_id' => $this->client_id,
            $this->prefix.'client_secret' => $this->client_secret,
            $this->prefix.'base_url' => $this->base_url,
            $this->prefix.'files_url' => $this->files_url,
            $this->prefix.'hospital_code' => $this->hospital_code
        ));
    }
}
