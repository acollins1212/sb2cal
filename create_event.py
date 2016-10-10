
#Written by A.J. Collins
#Reference Google Calendar API Documentation 
#Credit: used quickstart.py from Google API Docs as framework

#WARNING: THIS CODE WILL NOT WORK W/O schedule.txt and test_calendar_id.txt
#	 I'M NOT UPLOADING THEM TO GITHUB BECAUSE THEY CONTAIN PERSONAL INFO
#	 
#	 schedule:txt	copy-paste entire, detail-visible schedule from UCD Schedule Builder
#	 test_calendar_id.txt:   I created the calendar with the API and noted its id

from __future__ import print_function
import httplib2
import os
import sys

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
    schedule_str = "schedule.txt"


    '''
    with open ("test_calendar_id.txt", "r") as file:
        new_calendar_id = file.read()
        new_calendar_id = new_calendar_id[0:len(new_calendar_id)-1]
        print(new_calendar_id)
    '''
    new_calendar_id = createCalendar('WQ Course Schedule', 'Created by create_event.py', service)
    calendar = service.calendars().get(calendarId=new_calendar_id).execute()

    #Create the course array
    course_array = getCourseArray(schedule_str)

    for course in course_array:
        i = 0
        
        for meeting in course.meeting_array:
            event = course.getEventJSON(i)
            service.events().insert(calendarId=new_calendar_id, body=event).execute()
            i += 1




if __name__ == '__main__':
    main()



