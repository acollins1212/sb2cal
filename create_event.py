
#Written by A.J. Collins
#Reference Google Calendar API Documentation 
#Credit: used quickstart.py from Google API Docs as framework



from __future__ import print_function
import httplib2
import os

from apiclient import discovery
import oauth2client
from oauth2client import client
from oauth2client import tools

import datetime
from apiclient.discovery import build

from scheduler import *
######################################

try:
    import argparse
    flags = argparse.ArgumentParser(parents=[tools.argparser]).parse_args()
except ImportError:
    flags = None

# If modifying these scopes, delete your previously saved credentials
# at ~/.credentials/calendar-python-quickstart.json
SCOPES = 'https://www.googleapis.com/auth/calendar'
CLIENT_SECRET_FILE = 'client_secret.json'
APPLICATION_NAME = 'Google Calendar API Python Quickstart'


def get_credentials():
    """Gets valid user credentials from storage.

    If nothing has been stored, or if the stored credentials are invalid,
    the OAuth2 flow is completed to obtain the new credentials.

    Returns:
        Credentials, the obtained credential.
    """
    home_dir = os.path.expanduser('~')
    credential_dir = os.path.join(home_dir, '.credentials')
    if not os.path.exists(credential_dir):
        os.makedirs(credential_dir)
    credential_path = os.path.join(credential_dir,
                                   'calendar-python-quickstart.json')

    store = oauth2client.file.Storage(credential_path)
    credentials = store.get()
    if not credentials or credentials.invalid:
        flow = client.flow_from_clientsecrets(CLIENT_SECRET_FILE, SCOPES)
        flow.user_agent = APPLICATION_NAME
        if flags:
            credentials = tools.run_flow(flow, store, flags)
        else: # Needed only for compatibility with Python 2.6
            credentials = tools.run(flow, store)
        print('Storing credentials to ' + credential_path)
    return credentials

'''
#currently not using
def updateCalendar(test_calendar_id):    
    calendar = service.calendars().get(calendarId=test_calendar_id).execute()
    '''
    calendar['summary'] = "Testing Calendar"
    calendar['description'] = "Used for the Schedule Builder -> Google Calendar program I'm making"

    updated_calendar = service.calendars().update(calendarId=calendar['id'], body=calendar).execute()
    '''

    print(calendar['summary'])
    print(calendar['description'])
'''


#creates calendar and returns the calendar id
def createCalendar(name_of_calendar, description, SERVICE):
    calendar = {
    'summary': name_of_calendar,
    'description': description,
    'timeZone': 'America/Los_Angeles'
    }

    created_calendar = SERVICE.calendars().insert(body=calendar).execute()
    return created_calendar['id']
    

def main():
    
    credentials = get_credentials()
    http = credentials.authorize(httplib2.Http())
    service = build('calendar', 'v3', http=http)
  
    now = datetime.datetime.utcnow().isoformat() + 'Z' # 'Z' indicates UTC time  

    new_calendar_id = createCalendar('TESTING', 'test description', service)
    calendar = service.calendars().get(calendarId=new_calendar_id).execute()

    page_token = None
    while True:
        events = service.events().list(calendarId=new_calendar_id, pageToken=page_token).execute()
        for event in events['items']:
            print(event['summary'])
        page_token = events.get('nextPageToken')
        if not page_token:
            break
    '''
    print('Getting the upcoming 10 events')
    eventsResult = service.events().list(
        calendarId=test_calendar_id, timeMin=now, maxResults=10, singleEvents=True,
        orderBy='startTime').execute()
    
    events = eventsResult.get('items', [])
    print(events)

    if not events:
        print('No upcoming events found.')
    for event in events:
        start = event['start'].get('dateTime', event['start'].get('date'))
        print(start, event['summary'], event['id'])
    '''    



if __name__ == '__main__':
    main()



