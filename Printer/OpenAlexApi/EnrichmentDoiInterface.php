<?php
// Define la interfaz de enriquecimiento de instituciones
interface EnrichmentDoiInterface {
    public function enrichDoiData(string $doi);
}