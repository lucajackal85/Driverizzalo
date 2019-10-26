# Driverizzalo


## Quickstart
Complete the steps described in the rest of this page, and in about five minutes you'll have a simple PHP command-line application that makes requests to the Drive Data API.

The sample code used in this guide retrieves the channel resource for the GoogleDevelopers Drive channel and prints some basic information from that resource.
Prerequisites
To run this quickstart, you'll need:

## Step 1: Turn on the Drive Data API
- Use this wizard to create or select a project in the Google Developers Console and automatically turn on the API. Click Continue, then **Go to credentials**.
- On the **Add credentials to your project** page, click the **Cancel** button.
- At the top of the page, select the **OAuth consent screen** tab. Select an **Email address**, enter a **Product name** if not already set, and click the Save button.
- Select the **Credentials** tab, click the **Create credentials** button and select **OAuth client ID**.
- Select the application type Other, enter the name "Drive Data API Quickstart", and click the **Create** button.
- Click **OK** to dismiss the resulting dialog.
- Click the **file_download** (Download JSON) button to the right of the client ID.
- Move this file to your working directory and rename it ***client_secret.json***

## Step2: Create OAuth File
- Execute *bin/driverizzalo-credentials client_secret.json*
- Browse to url written on the output
- Allow application
- copy the request params in the address bar and paste to the terminal

