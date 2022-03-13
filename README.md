# ech-fb-feed
A Wordpress plugin to display Facebook feed of a Facebook page.

There is a "video" icon appears on the top right corner of the thumbnail in order to identify a video feed.  When the video thumbnail image is clicked, a lightbox will popup to display the video. 

Visitor can also shares the feed by clicking the "Share" button.


## Before installation
Before starting to use this plugin, you need to create a Facebook App of the Facebook page in order to get a "Permanent Page Access Token" that does not expire. Follow the instructions laid out in Facebook's [extending page tokens documentation](https://developers.facebook.com/docs/facebook-login/access-tokens#extendingpagetokens).

You can also follow the instructions below. Refer to the [answer(by donut)](https://stackoverflow.com/questions/17197970/facebook-permanent-page-access-token/28418469#28418469) in stack overflow

### 0. Create Facebook App
**If you already have an app**, skip to step 1
1. Go to [My Apps](https://www.facebook.com/login.php?next=https%3A%2F%2Fdevelopers.facebook.com%2Fapps%2F).
2. Click "+ Add a New App".
3. Setup a website app.


You don't need to change its permissions or anything. You just need an app that wont go away before you're done with your access token.


### 1. Get User Short-Lived Access Token
1. Go to the [Graph API Explorer](https://developers.facebook.com/tools/explorer).
2. Select the application you want to get the access token for (in the "Application" drop-down menu, not the "My Apps" menu).
3. Click "Get Token" > "Get User Access Token".
4. In the pop-up, under the "Extended Permissions" tab, check "manage_pages".
5. Click "Get Access Token".
6. Grant access from a Facebook account that has access to manage the target page. Note that if this user loses access the final, never-expiring access token will likely stop working.


The token that appears in the "Access Token" field is your short-lived access token.



### 2. Generate Long-Lived Access Token
Following these [instructions](https://developers.facebook.com/docs/facebook-login/access-tokens#extending) from the Facebook docs, make a GET request to
```
https://graph.facebook.com/v2.10/oauth/access_token?grant_type=fb_exchange_token&client_id={app_id}&client_secret={app_secret}&fb_exchange_token={short_lived_token}
```
entering in your app's ID and secret and the short-lived token generated in the previous step.

You **cannot use the Graph API Explorer**. For some reason it gets stuck on this request. I think it's because the response isn't JSON, but a query string. Since it's a GET request, you can just go to the URL in your browser.

The response should look like this:
`{"access_token":"ABC123","token_type":"bearer","expires_in":5183791}`

"ABC123" will be your long-lived access token. You can put it into the [Access Token Debugger](https://www.facebook.com/login.php?next=https%3A%2F%2Fdevelopers.facebook.com%2Ftools%2Fdebug%2Faccesstoken) to verify. Under "Expires" it should have something like "2 months".


### 3. Get User ID
Using the long-lived access token, make a GET request to
```
https://graph.facebook.com/v2.10/me?access_token={long_lived_access_token}
```
The `id` field is your account ID. You'll need it for the next step.


### 4. Get Permanent Page Access Token
Make a GET request to
```
https://graph.facebook.com/v2.10/{account_id}/accounts?access_token={long_lived_access_token}
```

The JSON response should have a `data` field under which is an array of items the user has access to. Find the item for the page you want the permanent access token from. The `access_token` field should have your permanent access token. Copy it and test it in the [Access Token Debugger](https://www.facebook.com/login.php?next=https%3A%2F%2Fdevelopers.facebook.com%2Ftools%2Fdebug%2Faccesstoken). Under "Expires" it should say "Never".


## Installation
After getting the Permanent Page Access Token:
1. Go to `inc/ech-fb-feed-functions.php` file
2. Search `$GLOBALS['perm_access_token'] = ` and `$GLOBALS['fb_page_id'] =` and paste the Permanent Page Access Token and the Facebook page ID respectively
3. Install the plugin


## Usage
To display the Facebook feed, enter shortcode: 
```
[ech_fb_feed]
```

### Shortcode attributes
- **limit**(INT): control how many posts to display on the first page. Default is 12. 

There is a "Load more" button to display more posts necessary.

## Screenshot
![](https://github.com/ktkeepgoing/ech-fb-feed/blob/ee974e39ad33b119ba0c446171cfaae80bc64109/screenshots/fb-feed-screenshot.jpg)



