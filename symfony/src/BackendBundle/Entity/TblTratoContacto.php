<?php

namespace BackendBundle\Entity;

/**
 * TblTratoContacto
 */
class TblTratoContacto
{
    /**
     * @var integer
     */
    private $idTratoContacto;

    /**
     * @var string
     */
    private $descTratoContacto;

    /**
     * @var string
     */
    private $inicialesTratoContacto;

    /**
     * @var boolean
     */
    private $activo = true;


    /**
     * Get idTratoContacto
     *
     * @return integer
     */
    public function getIdTratoContacto()
    {
        return $this->idTratoContacto;
    }

    /**
     * Set descTratoContacto
     *
     * @param string $descTratoContacto
     *
     * @return TblTratoContacto
     */
    public function setDescTratoContacto($descTratoContacto)
    {
        $this->descTratoContacto = $descTratoContacto;

        return $this;
    }

    /**
     * Get descTratoContacto
     *
     * @return string
     */
    public function getDescTratoContacto()
    {
        return $this->descTratoContacto;
    }

    /**
     * Set inicialesTratoContacto
     *
     * @param string $inicialesTratoContacto
     *
     * @return TblTratoContacto
     */
    public function setInicialesTratoContacto($inicialesTratoContacto)
    {
        $this->inicialesTratoContacto = $inicialesTratoContacto;

        return $this;
    }

    /**
     * Get inicialesTratoContacto
     *
     * @return string
     */
    public function getInicialesTratoContacto()
    {
        return $this->inicialesTratoContacto;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return TblTratoContacto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }
}

