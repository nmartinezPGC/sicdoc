<?php

namespace BackendBundle\Entity;

/**
 * TblDireccionesSreci
 */
class TblDireccionesSreci
{
    /**
     * @var string
     */
    private $codDireccionSreci;

    /**
     * @var integer
     */
    private $idDireccionSreci;

    /**
     * @var string
     */
    private $descDireccionSreci;

    /**
     * @var string
     */
    private $inicialesDireccionSreci;


    /**
     * Get codDireccionSreci
     *
     * @return string
     */
    public function getCodDireccionSreci()
    {
        return $this->codDireccionSreci;
    }

    /**
     * Set idDireccionSreci
     *
     * @param integer $idDireccionSreci
     *
     * @return TblDireccionesSreci
     */
    public function setIdDireccionSreci($idDireccionSreci)
    {
        $this->idDireccionSreci = $idDireccionSreci;

        return $this;
    }

    /**
     * Get idDireccionSreci
     *
     * @return integer
     */
    public function getIdDireccionSreci()
    {
        return $this->idDireccionSreci;
    }

    /**
     * Set descDireccionSreci
     *
     * @param string $descDireccionSreci
     *
     * @return TblDireccionesSreci
     */
    public function setDescDireccionSreci($descDireccionSreci)
    {
        $this->descDireccionSreci = $descDireccionSreci;

        return $this;
    }

    /**
     * Get descDireccionSreci
     *
     * @return string
     */
    public function getDescDireccionSreci()
    {
        return $this->descDireccionSreci;
    }

    /**
     * Set inicialesDireccionSreci
     *
     * @param string $inicialesDireccionSreci
     *
     * @return TblDireccionesSreci
     */
    public function setInicialesDireccionSreci($inicialesDireccionSreci)
    {
        $this->inicialesDireccionSreci = $inicialesDireccionSreci;

        return $this;
    }

    /**
     * Get inicialesDireccionSreci
     *
     * @return string
     */
    public function getInicialesDireccionSreci()
    {
        return $this->inicialesDireccionSreci;
    }
}

