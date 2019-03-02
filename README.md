# Sabbath sunset
Display sunset times for Sabbath from an iCalendar feed in Google Calendar, your phone or elsewhere.

## usage

Example calendar URL:

	http://sun.is.permanent.ee/?start=-100&end=365&filename=sabbath.ics

Modify the parameters in the URL according to your needs.

Add the above url into your Google Calendar at Other Calendars -> down arrow box thingie -> Add by URL

### Configuration
Copy config.example.php to config.php.

You will want to specify your own location and the timezone for that location.
The timezone is used for displaying the time inside the event description.

### start
What day to start the calendar on from today.

	start=-100

### end
What day to end the calendar on from today.

	end=365

### filename
This is the filename of the file offered for download when you access the calendar URL directly. This needs to be the last URL parameter, because otherwise Google Calendar will not read anything from this URL and will silently fail.

	filename=sunrise.ics
