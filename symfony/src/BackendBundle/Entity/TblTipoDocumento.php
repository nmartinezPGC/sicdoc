<?php

namespace BackendBundle\Entity;

/**
 * TblTipoDocumento
 */
class TblTipoDocumento
{
    /**
     * @var integer
     */
    private $idTipoDocumento;

    /**
     * @var string
     */
    private $codTipoDocumento;

    /**
     * @var string
     */
    private $descTipoDocumento;

    /**
     * @var string
     */
    private $inicialesTipoDocumento;


    /**
     * Get idTipoDocumento
     *
     * @return integer
     */
    public function getIdTipoDocumento()
    {
        return $this->idTipoDocumento;
    }

    /**
     * Set codTipoDocumento
     *
     * @param string $codTipoDocumento
     *
     * @return TblTipoDocumento
     */
    public function setCodTipoDocumento($codTipoDocumento)
    {
        $this->codTipoDocumento = $codTipoDocumento;

        return $this;
    }

    /**
     * Get codTipoDocumento
     *
     * @return string
     */
    public function getCodTipoDocumento()
    {
        return $this->codTipoDocumento;
    }

    /**
     * Set descTipoDocumento
     *
     * @param string $descTipoDocumento
     *
     * @return TblTipoDocumento
     */
    public function setDescTipoDocumento($descTipoDocumento)
    {
        $this->descTipoDocumento = $descTipoDocumento;

        return $this;
    }

    /**
     * Get descTipoDocumento
     *
     * @return string
     */
    public function getDescTipoDocumento()
    {
        return $this->descTipoDocumento;
    }

    /**
     * Set inicialesTipoDocumento
     *
     * @param string $inicialesTipoDocumento
     *
     * @return TblTipoDocumento
     */
    public function setInicialesTipoDocumento($inicialesTipoDocumento)
    {
        $this->inicialesTipoDocumento = $inicialesTipoDocumento;

        return $this;
    }

    /**
     * Get inicialesTipoDocumento
     *
     * @return string
     */
    public function getInicialesTipoDocumento()
    {
        return $this->inicialesTipoDocumento;
    }
}
