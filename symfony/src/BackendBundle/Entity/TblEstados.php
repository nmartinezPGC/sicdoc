<?php

namespace BackendBundle\Entity;

/**
 * TblEstados
 */
class TblEstados
{
    /**
     * @var string
     */
    private $codEstado;

    /**
     * @var integer
     */
    private $idEstado;

    /**
     * @var string
     */
    private $descripcionEstado;

    /**
     * @var string
     */
    private $inicalesEstado;

    /**
     * @var string
     */
    private $grupoEstado;


    /**
     * Get codEstado
     *
     * @return string
     */
    public function getCodEstado()
    {
        return $this->codEstado;
    }

    /**
     * Set idEstado
     *
     * @param integer $idEstado
     *
     * @return TblEstados
     */
    public function setIdEstado($idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado
     *
     * @return integer
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set descripcionEstado
     *
     * @param string $descripcionEstado
     *
     * @return TblEstados
     */
    public function setDescripcionEstado($descripcionEstado)
    {
        $this->descripcionEstado = $descripcionEstado;

        return $this;
    }

    /**
     * Get descripcionEstado
     *
     * @return string
     */
    public function getDescripcionEstado()
    {
        return $this->descripcionEstado;
    }

    /**
     * Set inicalesEstado
     *
     * @param string $inicalesEstado
     *
     * @return TblEstados
     */
    public function setInicalesEstado($inicalesEstado)
    {
        $this->inicalesEstado = $inicalesEstado;

        return $this;
    }

    /**
     * Get inicalesEstado
     *
     * @return string
     */
    public function getInicalesEstado()
    {
        return $this->inicalesEstado;
    }

    /**
     * Set grupoEstado
     *
     * @param string $grupoEstado
     *
     * @return TblEstados
     */
    public function setGrupoEstado($grupoEstado)
    {
        $this->grupoEstado = $grupoEstado;

        return $this;
    }

    /**
     * Get grupoEstado
     *
     * @return string
     */
    public function getGrupoEstado()
    {
        return $this->grupoEstado;
    }
}

