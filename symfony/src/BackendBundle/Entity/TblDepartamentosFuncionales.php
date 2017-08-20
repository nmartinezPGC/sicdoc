<?php

namespace BackendBundle\Entity;

/**
 * TblDepartamentosFuncionales
 */
class TblDepartamentosFuncionales
{
    /**
     * @var string
     */
    private $codDeptoFuncional;

    /**
     * @var integer
     */
    private $idDeptoFuncional;

    /**
     * @var string
     */
    private $descDeptoFuncional;

    /**
     * @var string
     */
    private $inicialesDeptoFuncional;

    /**
     * @var \BackendBundle\Entity\TblDireccionesSreci
     */
    private $codDireccionSreci;


    /**
     * Get codDeptoFuncional
     *
     * @return string
     */
    public function getCodDeptoFuncional()
    {
        return $this->codDeptoFuncional;
    }

    /**
     * Set idDeptoFuncional
     *
     * @param integer $idDeptoFuncional
     *
     * @return TblDepartamentosFuncionales
     */
    public function setIdDeptoFuncional($idDeptoFuncional)
    {
        $this->idDeptoFuncional = $idDeptoFuncional;

        return $this;
    }

    /**
     * Get idDeptoFuncional
     *
     * @return integer
     */
    public function getIdDeptoFuncional()
    {
        return $this->idDeptoFuncional;
    }

    /**
     * Set descDeptoFuncional
     *
     * @param string $descDeptoFuncional
     *
     * @return TblDepartamentosFuncionales
     */
    public function setDescDeptoFuncional($descDeptoFuncional)
    {
        $this->descDeptoFuncional = $descDeptoFuncional;

        return $this;
    }

    /**
     * Get descDeptoFuncional
     *
     * @return string
     */
    public function getDescDeptoFuncional()
    {
        return $this->descDeptoFuncional;
    }

    /**
     * Set inicialesDeptoFuncional
     *
     * @param string $inicialesDeptoFuncional
     *
     * @return TblDepartamentosFuncionales
     */
    public function setInicialesDeptoFuncional($inicialesDeptoFuncional)
    {
        $this->inicialesDeptoFuncional = $inicialesDeptoFuncional;

        return $this;
    }

    /**
     * Get inicialesDeptoFuncional
     *
     * @return string
     */
    public function getInicialesDeptoFuncional()
    {
        return $this->inicialesDeptoFuncional;
    }

    /**
     * Set codDireccionSreci
     *
     * @param \BackendBundle\Entity\TblDireccionesSreci $codDireccionSreci
     *
     * @return TblDepartamentosFuncionales
     */
    public function setCodDireccionSreci(\BackendBundle\Entity\TblDireccionesSreci $codDireccionSreci = null)
    {
        $this->codDireccionSreci = $codDireccionSreci;

        return $this;
    }

    /**
     * Get codDireccionSreci
     *
     * @return \BackendBundle\Entity\TblDireccionesSreci
     */
    public function getCodDireccionSreci()
    {
        return $this->codDireccionSreci;
    }
}
