<?php

/**
 * Interface for a service request
 */
interface iServiceRequestItem
{

    /**
     * Flags a request as SERVED
     * @return boolean Return TRUE if the serve process is successful
     */
    public function serve();

    /**
     * Cancels the request
     * @return boolean Returns TRUE if the request was successfully cancelled
     */
    public function cancel();

}