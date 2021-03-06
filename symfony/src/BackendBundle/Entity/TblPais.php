<?php

namespace BackendBundle\Entity;

/**
 * TblPais
 */
class TblPais
{
    /**
     * @var string
     */
    private $codPais;

    /**
     * @var integer
     */
    private $idPais;

    /**
     * @var string
     */
    private $descPais;

    /**
     * @var string
     */
    private $inicialesPais;

    /**
     * @var string
     */
    private $codigoPostal;

    /**
     * @var string
     */
    private $codContinente;


    /**
     * Get codPais
     *
     * @return string
     */
    public function getCodPais()
    {
        return $this->codPais;
    }

    /**
     * Set idPais
     *
     * @param integer $idPais
     *
     * @return TblPais
     */
    public function setIdPais($idPais)
    {
        $this->idPais = $idPais;

        return $this;
    }

    /**
     * Get idPais
     *
     * @return integer
     */
    public function getIdPais()
    {
        return $this->idPais;
    }

    /**
     * Set descPais
     *
     * @param string $descPais
     *
     * @return TblPais
     */
    public function setDescPais($descPais)
    {
        $this->descPais = $descPais;

        return $this;
    }

    /**
     * Get descPais
     *
     * @return string
     */
    public function getDescPais()
    {
        return $this->descPais;
    }

    /**
     * Set inicialesPais
     *
     * @param string $inicialesPais
     *
     * @return TblPais
     */
    public function setInicialesPais($inicialesPais)
    {
        $this->inicialesPais = $inicialesPais;

        return $this;
    }

    /**
     * Get inicialesPais
     *
     * @return string
     */
    public function getInicialesPais()
    {
        return $this->inicialesPais;
    }

    /**
     * Set codigoPostal
     *
     * @param string $codigoPostal
     *
     * @return TblPais
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return string
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set codContinente
     *
     * @param string $codContinente
     *
     * @return TblPais
     */
    public function setCodContinente($codContinente)
    {
        $this->codContinente = $codContinente;

        return $this;
    }

    /**
     * Get codContinente
     *
     * @return string
     */
    public function getCodContinente()
    {
        return $this->codContinente;
    }

    /**
     * Set codPais
     *
     * @param string $codPais
     *
     * @return TblPais
     */
    public function setCodPais($codPais)
    {
        $this->codPais = $codPais;

        return $this;
    }
}
