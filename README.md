

# ncn-civi-zoom
Civirules Conditions/Actions that talk with Zoom developed for NCN.

# what it does
This extension will connect CiviEvents with Zoom, allowing registrations for zoom events to be captured in your CiviCRM install via your website. This has a multitude of benefits, including GDPR compliance, using Zooms workflow, removing the need to manually export/import as well as being able to manage events in the same way you would any other CiviCRM events.

## Requirements

* PHP v7.0+
* CiviCRM 5.0+
* CiviRules extension

## Installation
* Install the extension in CiviCRM. More details [here](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#installing-a-new-extension) about installing extensions in CiviCRM.

## Setup
### Sign into Zoom and Create a JWT App

The JWT App within your Zoom account will allow you to connect CiviCRM to zoom, so that it can pass over participant information.

* Create a JWT app in your zoom account's [zoom market place](https://marketplace.zoom.us/develop/create) page using the instructions given in [the guide](https://marketplace.zoom.us/docs/guides/build/jwt-app).
* Keep a copy of the **API Key** and **API Secret Key** credentials, you'll need them to enter them into the CiviCRM Zoom settings

### Signing into CiviCRM and configure the Zoom settings
* Install the extension
* Navigate  to the zoom settings as **Events >> Zoom Settings**. 
* Create an entry for the zoom account (Note that the extension supports multiple Accounts)
* The Zoom Account ID will be used to indicate that the event belongs to a particular Zoom Account.
![Screenshot of add new zoom account button](images/add-new-zoom-account.jpg)

* On clicking the **Add New zoom account** button you'll be taken to a page where you need to enter the details of the new zoom account.
![Screenshot of add new zoom account settings page](images/add-new-zoom-account-setting-page.jpg)

* In the same settings page under the 'Commom zoom account settings' section, you need to select the custom fields for storing Webinar ID, Meeting ID and Zoom Account ID.
* You can create custom fields or use the existing ones created for events, to store Webinar ID , Meeting ID and Zoom Account ID.
* Along with that you also need to enter the 'Base url' in the same settings page.
![Screenshot of add common zoom settings](images/add-common-zoom-settings.jpg)

### Configuring an Event
* Once the settings page has been created then you can see the configured zoom account ids list as drop down box just above the Webinar Id field , whenever you Add/Edit an event as below.
* Once you've selected a zoom account and entered a Webinar Id / Meeting Id your webinar/meeting id will be verified with the selected zoom account along with a message next to the text box as below.
![Screenshot of event configuration](images/event-configuration.jpg)

## Actions
### Add to Zoom
* Must be paired with an `Event Participant` trigger and the linked conditions shall be the required custom fields such as Webinar Id / Meeting Id and the zoom account Id should not be empty.
* And the action for this rule should be as 'Add Participant to Zoom'.

This action takes the Zoom Webinar/Meeting ID and Zoom account ID from an event where a participant has just been added.
