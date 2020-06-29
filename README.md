# ncn-civi-zoom
Civirules Conditions/Actions that talk with Zoom developed for NCN.

## Requirements

* PHP v7.0+
* CiviCRM 5.0+

## Setup
After installing the extension navigate  to the zoom settings as 'Events->Zoom Settings' and enter the appropriate values to its keys.

## Actions
### Add to Zoom
Must be paired with an `Event Participant` trigger.

This action takes the Zoom Webinar/Meeting ID from an event where a participant has just been added. In NCN's case, the Custom fields of Zoom Webinar ID and Zoom Meeting ID are selected in the Zoom Settings Page.
