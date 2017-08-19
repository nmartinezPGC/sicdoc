<?php

namespace BackendBundle\Entity;

/**
 * TblTipoInstitucion
 */
class TblTipoInstitucion
{
    /**
     * @var string
     */
    private $codTipoInstitucion;

    /**
     * @var integer
     */
    private $idTipoInstitucion;

    /**
     * @var string
     */
    private $descTipoInstitucion;

    /**
     * @var string
     */
    private $inicialesTipoInstitucion;


    /**
     * Get codTipoInstitucion
     *
     * @return string
     */
    public function getCodTipoInstitucion()
    {
        return $this->codTipoInstitucion;
    }

    /**
     * Set idTipoInstitucion
     *
     * @param integer $idTipoInstitucion
     *
     * @return TblTipoInstitucion
     */
    public function setIdTipoInstitucion($idTipoInstitucion)
    {
        $this->idTipoInstitucion = $idTipoInstitucion;

        return $this;
    }

    /**
     * Get idTipoInstitucion
     *
     * @return integer
     */
    public function getIdTipoInstitucion()
    {
        return $this->idTipoInstitucion;
    }

    /**
     * Set descTipoInstitucion
     *
     * @param string $descTipoInstitucion
     *
     * @return TblTipoInstitucion
     */
    public function setDescTipoInstitucion($descTipoInstitucion)
    {
        $this->descTipoInstitucion = $descTipoInstitucion;

        return $this;
    }

    /**
     * Get descTipoInstitucion
     *
     * @return string
     */
    public function getDescTipoInstitucion()
    {
        return $this->descTipoInstitucion;
    }

    /**
     * Set inicialesTipoInstitucion
     *
     * @param string $inicialesTipoInstitucion
     *
     * @return TblTipoInstitucion
     */
    public function setInicialesTipoInstitucion($inicialesTipoInstitucion)
    {
        $this->inicialesTipoInstitucion = $inicialesTipoInstitucion;

        return $this;
    }

    /**
     * Get inicialesTipoInstitucion
     *
     * @return string
     */
    public function getInicialesTipoInstitucion()
    {
        return $this->inicialesTipoInstitucion;
    }
}

