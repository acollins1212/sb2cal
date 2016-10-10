import re
import pdb
import datetime

#Written by A.J. Collins
#scheduler.py takes the copy-paste in schedule.txt 
	#and parses everything up into Course objects

#CONSTANTS-----
FIRST_DAY = datetime.date(2017, 1, 9)
LAST_DAY = datetime.date(2017, 3, 18)

def parse_time(time_str):
	#time_str is expected as (X)X:XX AM/PM
	hour_minute = time_str.split(' ')[0]
	time = hour_minute.split(':')

	if time_str.split(' ')[1] == "PM":
		time[0] = int(time[0]) + 12
	time[0] = int(time[0])
	time[1] = int(time[1])	
	

	return time



#this class only contains one meeting type e.g. lecture, discussion, or lab 
class Course_meeting:
	
	def __init__(self, single_schedule):
		meeting_type_pattern = r'[a-zA-Z]+'
		start_time_pattern = r'\d{1,2}:\d{2} [A|P]M'
		end_time_pattern = r'- (\d{1,2}:\d{2} [A|P]M)'
		days_of_week_pattern = r'([A|P]M)([MTWRFS]+)[A-Z]'


		self.meeting_type = ''
		self.start_time = ''
		self.end_time = ''
		self.days_of_week = ''
		self.location = '' 
		
		#pdb.set_trace()

		#Taking meeting_type out of schedule passed in
		if re.search(meeting_type_pattern, single_schedule).group():
			self.meeting_type = re.search(meeting_type_pattern, single_schedule).group()
		else:
			self.meeting_type = 'meeting_type null'
	
		#Taking start_time out of schedule passed in
		if re.search(start_time_pattern, single_schedule):
			self.start_time = parse_time(re.search(start_time_pattern, single_schedule).group())
		else:
			self.start_time = 'start_time null'

		#Taking end_time out of schedule passed in
		if re.search(end_time_pattern, single_schedule):
			self.end_time = re.search(end_time_pattern, single_schedule)
			self.end_time = parse_time(self.end_time.group(1))
		else:
			self.end_time = 'end_time null'

		#Taking days_of_week out of schedule passed in
		if re.search(days_of_week_pattern, single_schedule):
			self.days_of_week = re.search(days_of_week_pattern, single_schedule)
			self.days_of_week = self.days_of_week.group(2)
			#RFC 5545 wants recurrence data given with two letter abbreviations
			self.days_of_week = self.days_of_week.replace('M', 'MO,')
			self.days_of_week = self.days_of_week.replace('T', 'TU,')
			self.days_of_week = self.days_of_week.replace('W', 'WE,')
			self.days_of_week = self.days_of_week.replace('R', 'TH,')
			self.days_of_week = self.days_of_week.replace('F', 'FR,')
			self.days_of_week = self.days_of_week[0:len(self.days_of_week)-1]

		else: 
			self.days_of_week = 'dow null'

		#Taking location out of schedule passed in
		if re.search(r'[A|P]M[MTWRFS]+([A-Z].*)', single_schedule):
			self.location = re.search(r'[A|P]M[MTWRFS]+([A-Z].*)', single_schedule).group(1)
		else:
			self.location = 'location null'	
		
	
	def print_Course_meeting(self):
		print()
		print(self.meeting_type)
		print(self.start_time)
		print(self.end_time)
		print(self.days_of_week)
		print(self.location)
			

#includes final exam
class Course:


	def __init__(self, class_string):
		name_pattern = r'(^[A-Z]{3} \d{3}.{0,1}) (.{3}) - (.+)'
		schedule_pattern = r'^.*[A|P]M .*'
		time_pattern = r'.*\d{1,2}:\d{2} [A|P]M'

		reg_obj_name = re.search(name_pattern, class_string, re.M)

		self.name_str = reg_obj_name.group(1)		#eg ABC 101 
		self.section = reg_obj_name.group(2)		
		self.description_str = reg_obj_name.group(3)	#title of class, like Intro to Biology
		
		self.finalExam_str = re.search(time_pattern, class_string, re.M).group()
		
		self.schedule_array = re.findall(schedule_pattern, class_string, re.M)
		
		self.meeting_array = []
		for i in range(len(self.schedule_array)):
			self.meeting_array.append(Course_meeting(self.schedule_array[i]))
		
		
		self.finalExam_date = re.search(r'\d{1,2}/\d{1,2}/\d{4}', self.finalExam_str).group()
		#The date is in MM/DD/YYYY format. split based on '/'
		self.finalExam_date = self.finalExam_date.split('/') 
		self.finalExam_date = datetime.date(int(self.finalExam_date[2]), int(self.finalExam_date[0]), int(self.finalExam_date[1]))

		self.finalExam_time = re.search(r'\d{1,2}:\d{2} [A|P]M', self.finalExam_str).group()
		self.finalExam_time = parse_time(self.finalExam_time)
		self.finalExam_time = datetime.time(self.finalExam_time[0], self.finalExam_time[1])

		self.units = re.search(r'Units: ([.\d]+)', class_string).group(1)

		
			
	def print_Course(self):
		print("{} - {}".format(self.name_str, self.description_str))
		print("Section:", self.section)
		print("Units:",self.units)
		print(self.finalExam_str)
		for m in self.meeting_array:
			m.print_Course_meeting()


	def getEventJSON(self, meeting_number):
		#course_code    type:string. example, "ABC 101"
		#course_meeting type:Course_meeting object - Lecture, Location, Time, Days of Week, etc
		#meeting_number refers to which Course_meeting from the array to return
		course_event = self.meeting_array[meeting_number]
		course_code = self.name_str

		start_time = datetime.time(course_event.start_time[0], course_event.start_time[1], 0)
		end_time = datetime.time(course_event.end_time[0], course_event.end_time[1], 0)

		#RECURRENCY STRINGS
		#concatenate the next three strings together for recurrence_string
		frequency_string = 'FREQ=WEEKLY;' 
		until_string = 'UNTIL={};'.format(datetime.datetime.combine(LAST_DAY,end_time).isoformat() + 'Z')
		byday_string = 'BYDAY={}'.format(course_event.days_of_week)
		recurrence_string = frequency_string + until_string + byday_string
		recurrence_string = re.sub('[:-]', '', recurrence_string) #JSON doesn't like colons and brackets...

		#addition allows the first date of a Tuesday class to be set as one day after the first day
                addition = 0
		if 'MO' in course_event.days_of_week:
			addition = 0
		elif 'TU' in course_event.days_of_week:
			addition = 1
		elif 'WE' in course_event.days_of_week:
			addtion = 2
		elif 'TH' in course_event.days_of_week:
			addition = 3
		elif 'FR' in course_event.days_of_week:
			addition = 4
		delta = datetime.timedelta(addition)
	
		event = {
		    'summary': "{} {}".format(course_code, course_event.meeting_type),
		    'location': course_event.location,
		    'description': "{}\nSection: {}".format(self.description_str, self.section),
		    'start': {
		        'dateTime': datetime.datetime.combine(FIRST_DAY + delta, start_time).isoformat(),
		        'timeZone': 'America/Los_Angeles',
		    },
		    'end': {
		        'dateTime': datetime.datetime.combine(FIRST_DAY + delta, end_time).isoformat(),
		        'timeZone': 'America/Los_Angeles',
		    },
		    'recurrence': [
		        'RRULE:WKST=SU;{}'.format(recurrence_string)
		    ],
		    'reminders': {
		        'useDefault': False,            
		        'overrides': [
				{'method': 'email', 'minutes': 24 * 60},
			        {'method': 'popup', 'minutes': 10},
		        ],
		     },
		}

		return event

	def getFinalJSON(self):
		#delta is two hours
		delta = datetime.timedelta(0,0,0,0,0,2,)
	
		event = {
		    'summary': "{} Final".format(self.name_str),
		    'description': "Good Luck!",
		    'start': {
		        'dateTime': datetime.datetime.combine(self.finalExam_date, self.finalExam_time).isoformat(),
		        'timeZone': 'America/Los_Angeles',
		    },
		    'end': {
		        'dateTime': (datetime.datetime.combine(self.finalExam_date, self.finalExam_time) + delta).isoformat(),
		        'timeZone': 'America/Los_Angeles',
		    },
		    'reminders': {
		        'useDefault': False,            
		        'overrides': [
				{'method': 'email', 'minutes': 24 * 60},
			        {'method': 'popup', 'minutes': 10},
		        ],
		     },
		}

		return event

		

def read_txt(schedule_str):
	
	print(schedule_str)

	
	with open (schedule_str, "r") as file:
		classes = file.read()

	return classes


def getCourseArray(schedule_str):

	
	with open (schedule_str, "r") as file:
		all_classes = file.read()
	

	class_strings = all_classes.split("Actions")

	course_array = []
	
	i = 0 
	for str in class_strings:
		course_array.append(Course(class_strings[i]))
		'''	
		course_array[i].print_Course()
		print('-----------------------------------------')
		'''
		i += 1

	return course_array

def main():
	getCourseArray("schedule.txt")

main()




