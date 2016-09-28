import re
import pdb

#Written by A.J. Collins
#scheduler.py takes the copy-paste in schedule.txt 
	#and parses everything up into Course objects


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
			self.start_time = re.search(start_time_pattern, single_schedule).group()
		else:
			self.start_time = 'start_time null'

		#Taking end_time out of schedule passed in
		if re.search(end_time_pattern, single_schedule):
			self.end_time = re.search(end_time_pattern, single_schedule)
			self.end_time = self.end_time.group(1)
		else:
			self.end_time = 'end_time null'

		#Taking days_of_week out of schedule passed in
		if re.search(days_of_week_pattern, single_schedule):
			self.days_of_week = re.search(days_of_week_pattern, single_schedule)
			self.days_of_week = self.days_of_week.group(2)
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
		
		self.scheduleArray = re.findall(schedule_pattern, class_string, re.M)
		
		self.meetingArray = []
		for i in range(len(self.scheduleArray)):
			self.meetingArray.append(Course_meeting(self.scheduleArray[i]))
		
		
		self.finalExam_date = re.search(r'\d{1,2}/\d{1,2}/\d{4}', self.finalExam_str).group()
		self.finalExam_time = re.search(r'\d{1,2}:\d{2} [A|P]M', self.finalExam_str).group()
		self.units = re.search(r'Units: ([.\d]+)', class_string).group(1)

		
			
	def print_Course(self):
		print("{} - {}".format(self.name_str, self.description_str))
		print("Section:", self.section)
		print("Units:",self.units)
		print(self.finalExam_str)
		for m in self.meetingArray:
			m.print_Course_meeting()


def read_txt(schedule_str):
	
	print(schedule_str)

	
	with open (schedule_str, "r") as file:
		classes = file.read()

	return classes

'''
def main():

	schedule_str = 'schedule.txt'
	with open (schedule_str, "r") as file:
		all_classes = file.read()
	

	class_strings = all_classes.split("Actions")

	course_array = []

	i = 0 
	for str in class_strings:
		course_array.append(Course(class_strings[i]))
	
		course_array[i].print_Course()
		i += 1
		print('-----------------------------------------')

main()
'''




