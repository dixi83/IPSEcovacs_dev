# IPS Ecovacs Deebot

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Software-Installation](#3-software-installation) 
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Anhang](#5-anhang)  
    1. [GUID der Module](#1-guid-der-module)
    2. [Changlog](#2-changlog)

## 1. Funktionsumfang

Ermöglich die Einbindung von Ecovacs Deebot roboter staubsaugers.

Folgende Module beinhaltet das Ecovacs Repository:

- Deebot

Currently known to work with the Ecovacs Deebot N79, M80 Pro, M81, M88 Pro, and R95 MKII

## 2. Voraussetzungen

 - IPS 4.3 oder höher  
 - Ecovacs Deebot staubsauger 

## 3. Software-Installation

**IPS 4.3:**  
   Bei privater Nutzung: Über das 'Module-Control' in IPS folgende URL hinzufügen.  
    `git://github.com/dixi83/IPSEcovacs.git`  

   **Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.**  

## 4. Einrichten der Instanzen in IP-Symcon

Ist direkt in der Dokumentation der jeweiligen Module beschrieben.  

## 5. Anhang

###  1. GUID der Module

 
| Modul   | Typ    | Prefix  | GUID                                   |
| :-----: | :----: | :-----: | :------------------------------------: |
| Deebot  | Device | EVDB    | {8eb4291c-8ec8-4e10-b5d7-1f90cc37bd8d} |


### 2. Changlog

Version 0.1:  
 - Erste start von das modul 

### 3. Special thanks:

F. Grutschus and his good php library for XMPP communication (https://github.com/fabiang/xmpp)
W. Pietri for his work on the good documented Python library/script (https://github.com/wpietri/sucks)
"Vaan Banaan" who helped me debuging the HTTP (communication https://gathering.tweakers.net/forum/list_messages/1869331)
"DJmaze" who helped me debugging the XMPP communication in the first tests (https://gathering.tweakers.net/forum/list_messages/1871583)