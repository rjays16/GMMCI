<?php
/**
 * Frontend application used by SegHIS
 *
 * @uses CWebApplication
 * @version $id$
 * @copyright Copyright &copy; 2015. Segworks Technologies Corporation
 * @author Alvin Quinones <ajqmuinones@segworks.com>
 */

class SegHis extends CWebApplication
{

    protected $_rootPath;

    /**
     *
     */
    public function setRootPath($path)
    {
        $this->_rootPath = $path;
    }

    /**
     *
     */
    public function getRootPath()
    {
        return $this->_rootPath;
    }

}
