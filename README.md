# FleetManager

Plugin WordPress permettant la gestion d'un parc automobile.

## Getting started

* Télécharger le dossier `FleetManager`
* Déplacer ce dossier dans `wp-content/plugins`
* Activer le plugin dans l'interface d'administration de WordPress
* (Optionnel) Importer les marques / modèles de vos véhicules depuis l'interface admin Réglages/FleetManager

## Paramétrage

Le paramétrage du plugin se fait grâce au fichier `settings.json`, présent à la racine du dossier `FleetManager`.

Exemple de configuration :

```
{
    "VehiclePostType" : {
        "display" : {
            "photo" : true,
            "FM_year" : true,
            "FM_doorNbr" : true,
            "FM_color" : true,
            "FM_warranty" : true
        },
        "default" : {
            "photoNbr" : 4
        }
    },
    "SocialNetwork" : {
        "facebook" : {
        	"enabled" : false,
        	"appId" : "my_app_id",
        	"appSecret" : "my_app_secret"
        }
    }
}
```

## Shortcodes

|Nom         |Description                               | Exemple                                       |
|------------|------------------------------------------|-----------------------------------------------|
|FM_photo_url|Retourne l'URL d'une photo d'un véhicule  |[FM_photo_url photo_id='1' post_id='1']        |
|FM_brand    |Déprécié Retourne la marque d'un véhicule |[FM_brand post_id='1']                         |
|FM_type     |Déprécié Retourne le type de véhicule     |[FM_type post_id='1']                          |
|FM_info     |Retourne une information d'un véhicule    |[FM_info info_name='year' post_id='1']         |


__FM_info info_name liste des paramètres__

* `FM_type` : Retourne le type
* `FM_brand` : Retourne la marque       
* `FM_model` : Retourne le modèle
* `FM_year` : Retourne l'année       
* `FM_price` : Retourne le prix      
* `FM_km` : Retourne le kilométrage         
* `FM_doorNbr` : Retourne le nombre de porte    
* `FM_chf` : Retourne la puissance fiscale         
* `FM_ch` : Retourne la puissance din          
* `FM_gearbox` : Retourne le type de la boîte de vitesse      
* `FM_fuel` : Retourne le type de carburant        
* `FM_circulation` : Retourne la date de mise en circulation
* `FM_color` : Retourne la couleur      
* `FM_warranty` : Retourne la garantie
* `FM_width` : Retourne la longueur      
* `FM_conso` : Retourne la consommation      
* `FM_trunk` : Retourne le volume du coffre

*__Remarque:__ Pour récupérer l'observation du véhicule, il faut récupérer le content de l'article*

## Importer

Importer des marques / modèles de véhicules au format CSV :

```
vehicle_brand;Citroën;citroen;0
vehicle_brand;C1;c1;citroen
vehicle_brand;C3;c3;citroen
```