-- explain analyze
select 
	a.staff_code,
    sbd.shift_type,
    sbd.dept,
    sbd.division,
    sbd.section,
    -- total working days
    case when sbd.shift_type = 'Shifting' 
		 then (
			SELECT day_count 
            FROM hrardb.calendars 
            WHERE MONTH = EXTRACT(MONTH FROM '2023-12-30') 
				AND shift_type = 'shifting'
		 ) * 8
		 else (
			SELECT day_count 
            FROM hrardb.calendars 
            WHERE MONTH = EXTRACT(MONTH FROM '2023-12-30') 
				AND shift_type = 'compressed'
		 ) * 9.25 end 
	as working_days,
	
    -- lwop calculation and conditions
    (
		SELECT count(lwop) * IF(sbd.shift_type = 'Shifting', 8, 9.25)
		FROM hrardb.attendances 
        WHERE staff_code = a.staff_code 
        and lwop > 0
			and  lwop > 0
			and (lwop > 0 and np is null)
            and	(lwop > 0 and other_leaves is null)
            and	(lwop > 0 and att_st  is null)
            and	(lwop > 0 and holiday is null)
            and	(lwop > 0 and att_end is null)
            and (lwop > 0 and (shift is null or shift != 'Rest Day')) 
	) as lwop,
    
    -- sl
    COUNT(
		case when a.leave_type = 'Sick Leave' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * IF(sbd.shift_type = 'Shifting', 8, 9.25) 
        as sl,
    
    -- sl: half day 
    SUM(
		case when a.leave_type = 'Sick Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * (IF(sbd.shift_type = 'Shifting', 8, 9.25) / 2) 
        as sl_half,
    
    -- sbl
    COUNT(
		case when a.leave_type = 'Sickness Benefit Leave' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * 8 
        as sbl,
        
	-- sbl: half day
    SUM(
		case when a.leave_type = 'Sickness Benefit Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as sbl_half,
        
	-- slbw
    COUNT(
		case when a.leave_type = 'Special Leave Benefits For Women(Magnacarta)' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * 8 
        as sblw,
	
    -- sblw: half day
    SUM(
		case when a.leave_type = 'Special Leave Benefits For Women(Magnacarta)' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as sblw_half,
	
    -- vl
	COUNT(
		case when a.leave_type = 'Vacation Leave' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * IF(sbd.shift_type = 'Shifting', 8, 9.25) 
        as vl,
    
    -- vl: half day 
    SUM(
		case when a.leave_type = 'Vacation Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * (IF(sbd.shift_type = 'Shifting', 8, 9.25) / 2)  
        as vl_half,
        
	-- aa
    COUNT(
		case when a.leave_type = 'Authorized Absent' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * IF(sbd.shift_type = 'Shifting', 8, 9.25) 
        as aa,
        
	-- aa: half day 
    SUM(
		case when a.leave_type = 'Authorized Absent' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * (IF(sbd.shift_type = 'Shifting', 8, 9.25) / 2) 
        as aa_half,
        
	-- el 
    COUNT(
		case when a.leave_type = 'Emergency Leave' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * 8 
        as el,
	
    -- el: half day
    SUM(
		case when a.leave_type = 'Emergency Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as el_half,
        
	-- late
    SUM(
		case when a.np is null 
        then a.late end
    ) / 60
    as late,
    
    -- early exit
    SUM(
		case when a.np is null 
        then a.early_exit end
    ) / 60
    as early_exit
FROM hrardb.attendances a
LEFT JOIN hrardb.staff_base_details sbd
	ON a.staff_code = sbd.staff_code
-- where a.staff_code = '0000244'
group by a.staff_code;

--