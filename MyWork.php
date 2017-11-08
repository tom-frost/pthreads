<?php

/**
 * MyWork is task what may be executed parallelly
 */
class MyWork extends Threaded
{

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        do {
            $value = null;

            $provider = $this->worker->getProvider();

            // Syncronize receiving data
            $provider->synchronized(function($provider) use (&$value) {
               $value = $provider->getNext();
            }, $provider);

            if ($value === null) {
                continue;
            }


            $logFile = 'logs/' . $this->id . '.log';
            file_put_contents($logFile, '');

            $this->check($value);
        }
        while ($value !== null);
    }

    function check($code)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'site_address');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'code' => $code,
            ]));

            $httpStatus = null;

            do {
                $serverOutput = curl_exec($ch);
                $httpStatus   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpStatus === 200 && !strrpos($serverOutput, 'WRONG')) {
                    echo 'success/pass ='.$code;
                    exit;
                } else {
                    if ($httpStatus !== 200) {
                        usleep(500000);
                    }
                }
            } while ($httpStatus !== 200);


            echo $code;

        curl_close($ch);

    }

}
