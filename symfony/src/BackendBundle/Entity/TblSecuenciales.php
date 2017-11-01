<?php

namespace BackendBundle\Entity;

/**
 * TblSecuenciales
 */
class TblSecuenciales
{
    /**
     * @var integer
     */
    private $idSecuencial;

    /**
     * @var string
     */
    private $codSecuencial;

    /**
     * @var string
     */
    private $valor1;

    /**
     * @var integer
     */
    private $valor2;

    /**
     * @var string
     */
    private $tablaSecuencia;


    /**
     * Get idSecuencial
     *
     * @return integer
     */
    public function getIdSecuencial()
    {
        return $this->idSecuencial;
    }

    /**
     * Set codSecuencial
     *
     * @param string $codSecuencial
     *
     * @return TblSecuenciales
     */
    public function setCodSecuencial($codSecuencial)
    {
        $this->codSecuencial = $codSecuencial;

        return $this;
    }

    /**
     * Get codSecuencial
     *
     * @return string
     */
    public function getCodSecuencial()
    {
        return $this->codSecuencial;
    }

    /**
     * Set valor1
     *
     * @param string $valor1
     *
     * @return TblSecuenciales
     */
    public function setValor1($valor1)
    {
        $this->valor1 = $valor1;

        return $this;
    }

    /**
     * Get valor1
     *
     * @return string
     */
    public function getValor1()
    {
        return $this->valor1;
    }

    /**
     * Set valor2
     *
     * @param integer $valor2
     *
     * @return TblSecuenciales
     */
    public function setValor2($valor2)
    {
        $this->valor2 = $valor2;

        return $this;
    }

    /**
     * Get valor2
     *
     * @return integer
     */
    public function getValor2()
    {
        return $this->valor2;
    }

    /**
     * Set tablaSecuencia
     *
     * @param string $tablaSecuencia
     *
     * @return TblSecuenciales
     */
    public function setTablaSecuencia($tablaSecuencia)
    {
        $this->tablaSecuencia = $tablaSecuencia;

        return $this;
    }

    /**
     * Get tablaSecuencia
     *
     * @return string
     */
    public function getTablaSecuencia()
    {
        return $this->tablaSecuencia;
    }
    /**
     * @var \BackendBundle\Entity\TblTipoDocumento
     */
    private $idTipoDocumento;


    /**
     * Set idTipoDocumento
     *
     * @param \BackendBundle\Entity\TblTipoDocumento $idTipoDocumento
     *
     * @return TblSecuenciales
     */
    public function setIdTipoDocumento(\BackendBundle\Entity\TblTipoDocumento $idTipoDocumento = null)
    {
        $this->idTipoDocumento = $idTipoDocumento;

        return $this;
    }

    /**
     * Get idTipoDocumento
     *
     * @return \BackendBundle\Entity\TblTipoDocumento
     */
    public function getIdTipoDocumento()
    {
        return $this->idTipoDocumento;
    }
    /**
     * @var string
     */
    private $reservada;


    /**
     * Set reservada
     *
     * @param string $reservada
     *
     * @return TblSecuenciales
     */
    public function setReservada($reservada)
    {
        $this->reservada = $reservada;

        return $this;
    }

    /**
     * Get reservada
     *
     * @return string
     */
    public function getReservada()
    {
        return $this->reservada;
    }
}
