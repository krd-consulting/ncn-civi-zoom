# ncn-civi-zoom
Civirules Conditions/Actions that talk with Zoom developed for NCN.

## Requirements

* PHP v7.0+
* CiviCRM 5.0+

## Setup
Create a `.env` file by renaming the `.env.example` and assigning appropriate values to its keys.

## Actions
### Add to Zoom
Must be paired with an `Event Participant` trigger. 

This action takes the Zoom Webinar ID from an event where a participant has just been added. In NCN's case, the Zoom Webinar ID
is in the custom field `custom_48`.
