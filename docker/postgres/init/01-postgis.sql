-- Activation de l'extension PostGIS pour les données géographiques
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS postgis_topology;

-- Vérification que PostGIS est bien installé
SELECT postgis_version();