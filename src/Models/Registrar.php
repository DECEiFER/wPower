<?php
namespace WeDevelopCoffee\wPower\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Domain model
 */
class Registrar extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tblregistrars';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['decodedValue'];

    /**
     * List all registrars.
     *
     * @return mixed
     */
    public function listRegistrars()
    {
        return self::groupBy('registrar');
    }

    /**
     * Fetch all registrar data for the specific registrar and decode.
     *
     * @return array
     */
    public function getRegistrarData()
    {
        $rawRegistrarData = $this->get();

        $registrarData = [];
        foreach($rawRegistrarData as $key => $data)
        {
            $registrarData [$data->registrar] [$data->setting] = $this->decode($data->value);
        }

        return $registrarData;
    }

    /**
     * Fetch all registrar data for the specific registrar and decode.
     *
     * @return array
     */
    public function getByKey($registrar, $key)
    {
        $data = self::where('registrar', $registrar)
            ->where('setting', $key)
            ->first();

        return self::decode($data->value);
    }

    /**
     * Get the TLDs
     *
     * @param $registrar
     * @return array
     */
    public function getTlds ($registrar)
    {
        $result = DomainPricing::where('autoreg', $registrar)->get();
        if(empty($result))
            return [];
        $tlds = [];
        foreach($result as $tld)
        {
            $tlds[$tld->extension] = $tld;
        }
        return $tlds;
    }

    /**
     * Estimate how many domains need a transfer.
     *
     * @return mixed
     */
    public function getDecodedValueAttribute()
    {
        return $this->decode($this->value);
    }

    /**
     * Decode the retrieved data.
     *
     * @param $data
     * @return mixed
     */
    protected function decode($data)
    {
        return html_entity_decode(\localAPI('DecryptPassword', ['password2' => $data])['password']);
    }

}
