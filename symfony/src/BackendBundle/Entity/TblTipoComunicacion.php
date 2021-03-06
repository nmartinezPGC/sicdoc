<?php

namespace BackendBundle\Entity;

/**
 * TblTipoComunicacion
 */
class TblTipoComunicacion
{
    /**
     * @var integer
     */
    private $idTipoComunicacion;

    /**
     * @var string
     */
    private $descTipoComunicacion;


    /**
     * Get idTipoComunicacion
     *
     * @return integer
     */
    public function getIdTipoComunicacion()
    {
        return $this->idTipoComunicacion;
    }

    /**
     * Set descTipoComunicacion
     *
     * @param string $descTipoComunicacion
     *
     * @return TblTipoComunicacion
     */
    public function setDescTipoComunicacion($descTipoComunicacion)
    {
        $this->descTipoComunicacion = $descTipoComunicacion;

        return $this;
    }

    /**
     * Get descTipoComunicacion
     *
     * @return string
     */
    public function getDescTipoComunicacion()
    {
        return $this->descTipoComunicacion;
    }
}
