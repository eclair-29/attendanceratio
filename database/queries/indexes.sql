create index attendances_staff_code_index on hrardb.attendances(staff_code, leave_type);
create index calendars_shift_year_month_index on hrardb.calendars(shift_type, year, month);
