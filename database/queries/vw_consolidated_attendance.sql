-- explain analyze
create view hrardb.vw_consolidated_attendance as 
select 
	a.staff_code,
    a.entity,
    sbd.shift_type,
    sbd.dept,
    sbd.division,
    sbd.section,
    -- total working days
    case when sbd.shift_type = 'Shifting' 
		 then (
			SELECT day_count 
            FROM hrardb.calendars 
            WHERE shift_type = 'shifting'
                and YEAR  = EXTRACT(YEAR  FROM (SELECT date FROM hrardb.attendances WHERE staff_code = a.staff_code LIMIT 1)) 
				and MONTH = EXTRACT(MONTH FROM (SELECT date FROM hrardb.attendances WHERE staff_code = a.staff_code LIMIT 1))
		 ) * 8
		 else (
			SELECT day_count 
            FROM hrardb.calendars 
            WHERE shift_type = 'compressed'
                and YEAR  = EXTRACT(YEAR  FROM (SELECT date FROM hrardb.attendances WHERE staff_code = a.staff_code LIMIT 1)) 
				and MONTH = EXTRACT(MONTH FROM (SELECT date FROM hrardb.attendances WHERE staff_code = a.staff_code LIMIT 1))
		 ) * 9.25 end 
	as working_days,
	
    -- lwop
    COUNT(
        case when a.lwop >= 1
             and (a.lwop >= 1 and a.np is null)
             and (a.lwop >= 1 and a.other_leaves is null)
             and (a.lwop >= 1 and a.att_st  is null)
             and (a.lwop >= 1 and a.holiday is null)
             and (a.lwop >= 1 and a.att_end is null)
             and (a.lwop >= 1 and (a.shift is null or a.shift != 'Rest Day')) 
        then a.lwop end ) * IF(sbd.shift_type = 'Shifting', 8, 9.25)
        as lwop,

    -- lwop: half day
    SUM(
        case when a.lwop < 1 and a.lwop != 0
             and (a.lwop < 1 and a.lwop != 0 and a.np is null)
             and (a.lwop < 1 and a.lwop != 0 and a.other_leaves is null)
             and (a.lwop < 1 and a.lwop != 0 and a.att_st  is null)
             and (a.lwop < 1 and a.lwop != 0 and a.holiday is null)
             and (a.lwop < 1 and a.lwop != 0 and a.att_end is null)
             and (a.lwop < 1 and a.lwop != 0 and (a.shift is null or a.shift != 'Rest Day')) 
        then a.lwop end ) * (IF(sbd.shift_type = 'Shifting', 8, 9.25) / 2)
        as lwop_half,
    
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
		then 1 
        else 0 end) * (IF(sbd.shift_type = 'Shifting', 8, 9.25) / 2) 
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
        
	-- pl
    COUNT(
		case when a.leave_type = 'Paternity Leave' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * 8 
        as pl,
        
	-- pl: half day
    SUM(
		case when a.leave_type = 'Paternity Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as pl_half,
        
	-- spl
    COUNT(
		case when a.leave_type = 'Solo Parent Leave' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * 8 
        as spl,
        
	-- spl: half day
    SUM(
		case when a.leave_type = 'Solo Parent Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as spl_half,
        
	-- vawc
    COUNT(
		case when a.leave_type = 'Violence Against Women / Children' 
			and a.other_leaves >= 1 
            and a.np is null 
		then 1 end) * 8 
        as vawc,
        
	-- vawc: half day
    SUM(
		case when a.leave_type = 'Violence Against Women / Children' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as vawc_half,
	
    -- wedding leave
    COUNT(
		case when a.leave_type = 'Wedding Leave' 
			and a.other_leaves >= 1 
            and a.np is null
		then 1 end) * 8 
        as wl,
        
	-- wedding leave: half day
    SUM(
		case when a.leave_type = 'Wedding Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as wl_half,
        
	-- offset 
    COUNT(
		case when a.leave_type = 'Offset Leave' 
			and a.other_leaves >= 1 
            and a.np is null
		then 1 end) * 8 
        as offset,
        
	-- offset: half day
	SUM(
		case when a.leave_type = 'Offset Leave' 
			and a.other_leaves < 1 
            and a.other_leaves != 0 
            and a.np is null
		then 1 end) * 4
        as offset_half,
        
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
-- where a.staff_code = '0100998'
where sbd.staff_code not in ('999998') -- R,Guest2
group by a.staff_code, a.entity;

--