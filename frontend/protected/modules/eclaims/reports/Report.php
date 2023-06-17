<?php

/**
 * Report.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2015. Segworks Technologies Corporation
 */



use Java;
use JavaException;
use JavaClass;
use SegHis\modules\reports\CompileException;
use Yii;

// var_dump(Yii::getPathOfAlias('application.frontend.modules.eclaims.reports'));die;
// require_once Yii::getPathOfAlias('application.frontend.modules.eclaims.reports') . '/Java.inc';
require_once getcwd() . '\frontend\protected\modules\eclaims\reports\Java.inc';


/**
 * Base class for all Jasper Report generators
 *
 * @
 */
class Report extends \CComponent
{
    const FORMAT_PDF = 'pdf';
    const FORMAT_HTML = 'html';
    const FORMAT_DOCX = 'docx';
    const FORMAT_XLS = 'excel';
    /**
     * @var string
     */
    public $template;
    /**
     * @var string
     */
    public $format = self::FORMAT_PDF;
    /**
     * @var array
     */
    public $params = array();
    /**
     * @var array
     */
    public $data = array();
    /**
     * @var mixed
     */
    private $jasperReport;
    /**
     * @var mixed
     */
    private $jasperPrint;
    /**
     * @var string
     */
    private $outputBuffer;
    /**
     * @var array
     */
    private $subreports = array();


    /**
     *
     *
     * @param $key string Parameter key for the subreport
     * @param string $subReportPath The path to the .jrxml of the subreport
     *
     * @todo Add exception handlers
     */
    public function addSubReport($key, $subReportPath)
    {
        $this->subreports[$key] = $subReportPath;
    }


    /**
     * @param bool $recompile
     *
     * @return mixed
     *
     * @throws CompileException
     */
    public function compile($recompile = false)
    {
        if (empty($this->jasperReport) || $recompile) {
            $compileManager = new \JavaClass('net.sf.jasperreports.engine.JasperCompileManager');
            try {
                $this->jasperReport = $compileManager->compileReport(realpath($this->template));
            } catch (JavaException $e) {
		echo $e;
		Yii::app()->end();
//                throw new CompileException('', $e);
            }

            // Compile subreports
            foreach ($this->subreports as $key => $path) {
                try {
                    $this->params[$key] = $compileManager->compileReport(realpath($path));
                } catch (JavaException $e) {
                    echo $e;
                    Yii::app()->end();
//                    throw new CompileException('');
                }
            }
        }

        return $this->jasperReport;
    }

    /**
     * @param bool $regenerate
     *
     * @return mixed
     *
     * @throws CompileException
     */
    public function generate($regenerate = false)
    {
        $this->compile();
        if (empty($this->jasperPrint) || $regenerate) {
            $fillManager = new \JavaClass("net.sf.jasperreports.engine.JasperFillManager");
            try {
                $this->jasperPrint = $fillManager->fillReport(
                    $this->compile(),
                    $this->params,
                    $this->createDataSourceFromArray($this->data)
                );
            } catch (JavaException $e) {
                echo $e;
                Yii::app()->end();
//                throw new FillReportException('');
            }
        }

        return $this->jasperPrint;
    }

    /**
     * Renders the report
     * @return string
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function display($regenerate = false, $return = false)
    {

        // var_dump($this->params);die;
        $this->generate();
        if (empty($this->outputBuffer) || $regenerate) {
            switch ($this->format) {
                case self::FORMAT_XLS:
                    $tmpFile = tempnam(sys_get_temp_dir(), 'jrx');
                    @file_put_contents($tmpFile, '');
                    @chmod($tmpFile, 0666);

                    $exporter = new Java("net.sf.jasperreports.engine.export.JExcelApiExporter");
                    $xlsParam = new Java("net.sf.jasperreports.engine.export.JRXlsExporterParameter");
                    $param = new Java("net.sf.jasperreports.engine.JRExporterParameter");
                    $bool = new Java("java.lang.Boolean");

                    $exporter->setParameter($param->JASPER_PRINT, $this->jasperPrint);
                    $exporter->setParameter($param->OUTPUT_FILE_NAME, $tmpFile);
                    $exporter->setParameter($xlsParam->IS_REMOVE_EMPTY_SPACE_BETWEEN_ROWS, $bool->TRUE);
                    $exporter->setParameter($xlsParam->IS_DETECT_CELL_TYPE, $bool->TRUE);
                    $exporter->exportReport();
                    $this->outputBuffer = @file_get_contents($tmpFile);

                    header('Content-Type: application/xls');
                    header('Content-Disposition: attachment; filename=report.xls');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    header('Cache-Control: private');
                    header('Pragma: private');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    break;
                case self::FORMAT_PDF:
                default:
                    header('Content-Type: application/pdf');
                    $exportManager = new \JavaClass("net.sf.jasperreports.engine.JasperExportManager");
                    $this->outputBuffer = $exportManager->exportReportToPdf($this->jasperPrint);
                    break;
            }
        }
        if ($return) {
            return $this->outputBuffer;
        }

        echo $this->outputBuffer;
        return $this->outputBuffer;
    }


    /**
     * @param string $name
     * @param mixed $value
     */
    public function setParameter($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     *
     * @param array $data
     *
     * @return Java
     */
    public function createDataSourceFromArray(array $data)
    {
        $jCollection = new Java('java.util.ArrayList');
        foreach ($data as $row) {
            $jHashMap = new Java('java.util.HashMap');
            foreach ($row as $field => $value) {
                $jHashMap->put($field, $value);
            }
            $jCollection->add($jHashMap);
        }

        return new Java("net.sf.jasperreports.engine.data.JRMapCollectionDataSource", $jCollection);
    }
}
