-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 11, 2019 at 02:33 PM
-- Server version: 5.5.21
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rayarcca_admin`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `create_master_view`(IN tbl_name VARCHAR(64))
begin
DECLARE done INTEGER DEFAULT 0;
DECLARE domainCount INTEGER  DEFAULT 0;
		 DECLARE indexCount INTEGER  DEFAULT 0;
         DECLARE domainId TINYINT;
		 DECLARE domainName VARCHAR(64);
 DECLARE master_cursor CURSOR FOR SELECT domain_id, domain_name FROM rayarcca_admin.domain WHERE status not in ('PENDING','ARCHIVED') and status is not NULL;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

DROP VIEW IF EXISTS tbl_name;
SET @query = CONCAT('CREATE VIEW ',tbl_name,' AS '); 


 OPEN master_cursor;
 select FOUND_ROWS() into domainCount;
SELECT domainCount;
 get_items: LOOP
	
 IF done = 1 THEN 
 LEAVE get_items;
 END IF;
 
 FETCH master_cursor INTO domainId,domainName;
 SET @query =  CONCAT(@query,'SELECT *, ',domainId,' as domain_id FROM rayarcca_', domainName,'.',tbl_name,' ');
  select @query; 
 IF indexCount<>domainCOUNT THEN
SET @query=CONCAT(@query,'UNION ');
 END IF;

SET indexCount=indexCount+1;
END LOOP get_items;
 
 CLOSE master_cursor;
 select @query; 
PREPARE stmt from @query; 
EXECUTE stmt; 
DEALLOCATE PREPARE stmt;
end$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_detail_stats`()
BEGIN
	DECLARE done INTEGER DEFAULT 0;
    DECLARE vtarget_date DATE;
 	DECLARE runtime_cursor CURSOR FOR 
		SELECT target_date FROM cross_domain_calendar
		WHERE  DATE(target_date)< DATE(NOW())
		AND DATE(target_date)>= 
		(
			SELECT IFNULL(MAX(DATE(runtime)),SUBDATE(CURDATE(),1)) as last_rundate 
			FROM rayarcca_admin.job_runtime_history
			WHERE job_id=7
		);
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
	TRUNCATE TABLE s1_cross_domain_detail_statistics;
	TRUNCATE TABLE s2_cross_domain_detail_statistics; 
	TRUNCATE TABLE s3_cross_domain_detail_statistics; 
	TRUNCATE TABLE s4_cross_domain_detail_statistics; 
	
	OPEN runtime_cursor;
	get_staging_detail_stats: LOOP
 		IF done = 1 THEN 
 			LEAVE get_staging_detail_stats;
 		END IF;
 
 		FETCH runtime_cursor INTO vtarget_date;
		INSERT s1_cross_domain_detail_statistics
		SELECT *
		FROM (
			SELECT IFNULL(view_count,0) as view_count, IFNULL(like_count,0) as like_count, 				        	view_date as target_date, vc.domain_id as domain_id
			FROM cross_domain_view_count_statistics vc
			LEFT JOIN
			cross_domain_like_count_statistics lc
			ON view_date=like_date and vc.domain_id=lc.domain_id
			UNION ALL
			SELECT IFNULL(view_count,0) as view_count,  IFNULL(like_count,0) as like_count, 		        	like_date as target_date, lc.domain_id as domain_id
			FROM cross_domain_view_count_statistics vc
			RIGHT JOIN
			cross_domain_like_count_statistics lc
			ON view_date=like_date and vc.domain_id=lc.domain_id
		)s1_view_count_like_count
		WHERE target_date = vtarget_date;

		INSERT s2_cross_domain_detail_statistics 
		SELECT *
		FROM (
			SELECT IFNULL(ic.item_count,0) as item_count,IFNULL(view_count,0) AS 		view_count,IFNULL(like_count,0) AS like_count,target_date, s1.domain_id as domain_id 
			FROM s1_cross_domain_detail_statistics s1
			LEFT JOIN cross_domain_rolling_item_count_statistics ic
			ON target_date=last_item_added_date and s1.domain_id=ic.domain_id
			UNION ALL
			SELECT IFNULL(ic.item_count,0) as item_count,IFNULL(view_count,0) AS 		view_count,IFNULL(like_count,0) AS like_count,last_item_added_date as target_date, ic.domain_id as domain_id 
			FROM s1_cross_domain_detail_statistics s1
			RIGHT JOIN cross_domain_rolling_item_count_statistics ic
			ON target_date=last_item_added_date and s1.domain_id=ic.domain_id
		) s2_item_count
		WHERE target_date = vtarget_date;

		INSERT s3_cross_domain_detail_statistics 
		SELECT *
		FROM (
			SELECT distinct IFNULL(s2.item_count,0) as item_count,IFNULL(view_count,0) AS 	view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,target_date, s2.domain_id as domain_id 
			FROM s2_cross_domain_detail_statistics s2
			LEFT JOIN cross_domain_rolling_max_view_item_statistics mvi
			ON target_date=last_item_view_date and s2.domain_id=mvi.domain_id
			UNION ALL
			SELECT distinct IFNULL(s2.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,last_item_view_date as target_date, mvi.domain_id as domain_id 
			FROM s2_cross_domain_detail_statistics s2
			RIGHT JOIN cross_domain_rolling_max_view_item_statistics mvi
			ON target_date=last_item_view_date and s2.domain_id=mvi.domain_id
		) s3_max_view_count
		WHERE target_date = vtarget_date;
		
		INSERT s4_cross_domain_detail_statistics 
		SELECT *
		FROM (
			SELECT distinct IFNULL(s3.item_count,0) as item_count,IFNULL(view_count,0) AS 	view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,IFNULL(max_item_like_count,0) as max_item_like_count,target_date, s3.domain_id as domain_id 
			FROM s3_cross_domain_detail_statistics s3
			LEFT JOIN cross_domain_rolling_max_like_item_statistics mli
			ON target_date=last_item_like_date and s3.domain_id=mli.domain_id
			UNION ALL
			SELECT distinct IFNULL(s3.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,IFNULL(max_item_like_count,0) as max_item_like_count,last_item_like_date as target_date, mli.domain_id as domain_id 
			FROM s3_cross_domain_detail_statistics s3
			RIGHT JOIN cross_domain_rolling_max_like_item_statistics mli
			ON target_date=last_item_like_date and s3.domain_id=mli.domain_id
		) s4_max_like_count
		WHERE target_date = vtarget_date;

	END LOOP get_staging_detail_stats;
 	CLOSE runtime_cursor;
	
	INSERT cross_domain_detail_statistics
SELECT NULL, item_count,view_count,max_item_view_count as  view_count_max_by_item,like_count,max_item_like_count as  like_count_max_by_item,target_date as created,domain_id from s4_cross_domain_detail_statistics; 
	
  	INSERT job_runtime_history(job_id) VALUES
	(7);
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_like_count_stats`()
BEGIN
DECLARE vlast_rundate DATE;
DECLARE runtime_cursor CURSOR FOR 
SELECT IFNULL(MAX(DATE(runtime)), SUBDATE(CURDATE(),1)) as last_rundate FROM rayarcca_admin.job_runtime_history
where job_id=3;
OPEN runtime_cursor;
FETCH runtime_cursor INTO vlast_rundate;

	INSERT cross_domain_like_count_statistics
	SELECT NULL,SUM( like_value ) AS like_count, DATE( like_time ) AS like_date, domain_id
	FROM rayarcca_admin.member_item_like
	WHERE DATE(like_time)>= vlast_rundate
	AND DATE(like_time)< DATE(NOW())
	GROUP BY domain_id, DATE( like_time ) ;

CLOSE runtime_cursor;
INSERT job_runtime_history(job_id) VALUES
(3);	
	
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_rolling_item_count_stats`()
BEGIN
DECLARE done INTEGER DEFAULT 0;
DECLARE vlast_rundate DATE;
DECLARE runtime_cursor CURSOR FOR 
SELECT target_date FROM cross_domain_calendar
WHERE  DATE(target_date)< DATE(NOW())
AND DATE(target_date)>= 
(
SELECT IFNULL(MAX(DATE(runtime)),CURDATE()) as last_rundate FROM rayarcca_admin.job_runtime_history
where job_id=1
);

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
DROP TEMPORARY TABLE IF EXISTS t_item_count;
CREATE TEMPORARY TABLE t_item_count (
	item_count INT,
    last_item_added_date DATE,
	domain_id tinyint unsigned NOT NULL
);
 OPEN runtime_cursor;

 get_daily: LOOP

 IF done = 1 THEN 
 LEAVE get_daily;
 END IF;
 
FETCH runtime_cursor INTO vlast_rundate;
INSERT t_item_count
SELECT item_count,last_item_date_added,domain_id
FROM
(
SELECT count(*) as item_count, Max(DATE(item_date_added)) as last_item_date_added,domain_id
FROM rayarcca_admin.item
WHERE item_status="PUBLISHED"
AND DATE(item_date_added)<= SUBDATE(vlast_rundate,1)
GROUP BY domain_id
) item_count_by_date;


END LOOP get_daily;
CLOSE runtime_cursor;
INSERT rayarcca_admin.cross_domain_rolling_item_count_statistics
SELECT NULL,item_count, last_item_added_date,domain_id
FROM (
SELECT distinct item_count, last_item_added_date,domain_id
FROM t_item_count ORDER BY domain_id,item_count
) distinct_item_count;



INSERT job_runtime_history(job_id) VALUES
(1);
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_rolling_max_like_item_stats`()
begin
DECLARE done INTEGER DEFAULT 0;
         DECLARE last_like_date DATE;
 DECLARE runtime_cursor CURSOR FOR 
SELECT target_date FROM cross_domain_calendar
WHERE  DATE(target_date)< DATE(NOW())
AND DATE(target_date)>= 
(
SELECT IFNULL(MAX(DATE(runtime)),SUBDATE(CURDATE(),1)) as last_rundate FROM rayarcca_admin.job_runtime_history
where job_id=5
);
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

DROP TEMPORARY TABLE IF EXISTS t_max_like_count;
CREATE TEMPORARY TABLE t_max_like_count (
	item_id INT,
	like_count INT,
    last_like_time DATE,
	domain_id tinyint unsigned NOT NULL
);

 OPEN runtime_cursor;
 get_maxdates: LOOP

 IF done = 1 THEN 
 LEAVE get_maxdates;
 END IF;
 
 FETCH runtime_cursor INTO last_like_date;
INSERT t_max_like_count
SELECT lcid.item_id,lcid.like_count,last_like_time,lcid.domain_id
FROM
(
	SELECT item_id, SUM( like_value ) as like_count, MAX(DATE( like_time )) as last_like_time,domain_id
	FROM rayarcca_admin.member_item_like
	WHERE DATE( like_time ) <= last_like_date
	GROUP BY domain_id,item_id
) lcid
JOIN
(
	SELECT MAX(like_count) as like_count,domain_id
	FROM
	(
		SELECT item_id, SUM( like_value ) as like_count, MAX(DATE( like_time )) as last_like_time,domain_id
		FROM rayarcca_admin.member_item_like
		WHERE DATE( like_time ) <= last_like_date
		GROUP BY domain_id,item_id
	) like_count_by_item
	GROUP BY domain_id
)mlid
ON lcid.like_count=mlid.like_count
AND lcid.domain_id=mlid.domain_id;


END LOOP get_maxdates;
 CLOSE runtime_cursor;
 INSERT rayarcca_admin.cross_domain_rolling_max_like_item_statistics
 SELECT NULL,item_id, like_count,last_like_time, domain_id
FROM (
SELECT distinct item_id, like_count,last_like_time, domain_id
FROM t_max_like_count ORDER BY last_like_time
) distinct_max_like_count;
 

  INSERT job_runtime_history(job_id) VALUES
(5);
end$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_rolling_max_view_item_stats`()
BEGIN
DECLARE done INTEGER DEFAULT 0;
DECLARE last_view_date DATE;
DECLARE runtime_cursor CURSOR FOR 
SELECT target_date FROM cross_domain_calendar
WHERE  DATE(target_date)< DATE(NOW())
AND DATE(target_date)>= 
(
SELECT IFNULL(MAX(DATE(runtime)),SUBDATE(CURDATE(),1)) as last_rundate FROM rayarcca_admin.job_runtime_history
where job_id=4
);
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
DROP TEMPORARY TABLE IF EXISTS t_max_view_count;
CREATE TEMPORARY TABLE t_max_view_count (
	item_id INT,
	view_count INT,
    last_access_time DATE,
	domain_id tinyint unsigned NOT NULL
);

OPEN runtime_cursor;
get_maxdates: LOOP

	IF done = 1 THEN 
		LEAVE get_maxdates;
 	END IF;
 
	FETCH runtime_cursor INTO last_view_date;

	INSERT t_max_view_count
	SELECT vcid.item_id,vcid.view_count,last_access_time,vcid.domain_id
	FROM
	(
		SELECT item_id,count(ip_address) as view_count,MAX(DATE(access_time)) as 	last_access_time,domain_id
		FROM rayarcca_admin.member_item_access
		WHERE DATE(access_time)<= last_view_date
		GROUP BY domain_id,item_id
	) vcid
	JOIN
	(	SELECT MAX(view_count) as view_count,domain_id
		FROM
		(
			SELECT item_id,count(ip_address) as view_count,  MAX(DATE(access_time)) as 	last_access_time,domain_id
			FROM rayarcca_admin.member_item_access
			WHERE DATE(access_time)<= last_view_date
			GROUP BY domain_id,item_id
		) view_count_by_item2	
		GROUP BY domain_id
	)mvid
	ON vcid.view_count=mvid.view_count
	AND vcid.domain_id=mvid.domain_id;

END LOOP get_maxdates;
CLOSE runtime_cursor;
INSERT rayarcca_admin.cross_domain_rolling_max_view_item_statistics
SELECT NULL,item_id, view_count,last_access_time, domain_id
FROM (
	SELECT distinct item_id, view_count,last_access_time, domain_id
	FROM t_max_view_count ORDER BY last_access_time
) distinct_max_view_count;
INSERT job_runtime_history(job_id) VALUES
(4);
end$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_summary_stats`()
BEGIN
DECLARE done INTEGER DEFAULT 0;
DECLARE vlast_rundate DATE;
DECLARE runtime_cursor CURSOR FOR 
SELECT target_date FROM cross_domain_calendar
WHERE  DATE(target_date)< DATE(NOW())
AND DATE(target_date)>= 
(
SELECT IFNULL(MAX(DATE(runtime)),SUBDATE(CURDATE(),1)) as last_rundate FROM rayarcca_admin.job_runtime_history
where job_id=6
);

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

OPEN runtime_cursor;

 get_daily: LOOP

 IF done = 1 THEN 
 LEAVE get_daily;
 END IF;
FETCH runtime_cursor INTO vlast_rundate;
INSERT cross_domain_summary_statistics(stats_id,item_count,like_count,view_count,like_count_max_by_item,view_count_max_by_item,created)
SELECT NULL
,IFNULL((
	SELECT count(*) As item_count 
	FROM rayarcca_admin.item 
	WHERE item_status="PUBLISHED"
	AND item_date_added<=vlast_rundate
),0) as item_count
, IFNULL((
	SELECT sum(like_value) as like_count 
	FROM rayarcca_admin.member_item_like
	WHERE like_time<=vlast_rundate
),0) as like_count
, IFNULL((SELECT count(*)
	FROM (
		SELECT distinct ip_address, item_id,domain_id
		FROM rayarcca_admin.member_item_access 
		WHERE access_type="S"
		AND access_time<=vlast_rundate
	) distinct_views
),0) as view_count
,IFNULL((Select max(like_count_by_item)   
	FROM(
		SELECT sum(like_value) as like_count_by_item  
		FROM rayarcca_admin.member_item_like
		WHERE like_time<=vlast_rundate
		group by domain_id,item_id
	)max_member_item_like_master_by_item
),0) as like_count_max_by_item
,IFNULL((Select max(view_count_by_item)   
	FROM(
			Select count(distinct(ip_address)) as view_count_by_item  
			from rayarcca_admin.member_item_access 
			WHERE access_time<=vlast_rundate
			group by domain_id,item_id
		)max_member_item_access_master_by_item
),0) as view_count_max_by_item,
vlast_rundate as created
;
END LOOP get_daily;
CLOSE runtime_cursor;

INSERT job_runtime_history(job_id) VALUES
(6);
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `daily_cross_domain_view_count_statistics`()
BEGIN
DECLARE vlast_rundate DATE;
DECLARE runtime_cursor CURSOR FOR 
SELECT IFNULL(MAX(DATE(runtime)), SUBDATE(CURDATE(),1)) as last_rundate FROM rayarcca_admin.job_runtime_history
where job_id=2;
OPEN runtime_cursor;
FETCH runtime_cursor INTO vlast_rundate;

INSERT cross_domain_view_count_statistics
SELECT NULL,count(*) as view_count, view_date, domain_id
	FROM
	(
		SELECT dv.ip_address,dv.item_id,min(DATE(access_time)) as view_date,dv.domain_id
		FROM 
		(
			SELECT distinct ip_address, item_id,domain_id
			FROM rayarcca_admin.member_item_access
		) dv
		JOIN rayarcca_admin.member_item_access mia
		ON dv.ip_address=mia.ip_address and dv.item_id=mia.item_id and dv.domain_id=mia.domain_id
		GROUP BY dv.ip_address,dv.item_id,dv.domain_id
	) one_time_per_ip_view
	WHERE view_date>= vlast_rundate
	AND view_date< DATE(NOW())
	GROUP BY domain_id,view_date;
CLOSE runtime_cursor;
INSERT job_runtime_history(job_id) VALUES
(2);
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `insert_cross_domain_detail_statistics`()
BEGIN

DROP TABLE IF EXISTS s1_cross_domain_detail_statistics; 
CREATE TABLE s1_cross_domain_detail_statistics AS
SELECT IFNULL(view_count,0) as view_count, IFNULL(like_count,0) as like_count, view_date as target_date, 
vc.domain_id as domain_id
FROM cross_domain_view_count_statistics vc
LEFT JOIN
cross_domain_like_count_statistics lc
ON view_date=like_date and vc.domain_id=lc.domain_id
UNION ALL
SELECT view_count, like_count, like_date as target_date, 
lc.domain_id as domain_id
FROM cross_domain_view_count_statistics vc
RIGHT JOIN
cross_domain_like_count_statistics lc
ON view_date=like_date and vc.domain_id=lc.domain_id
ORDER BY target_date,domain_id;


DROP TABLE IF EXISTS s2_cross_domain_detail_statistics; 
CREATE TABLE s2_cross_domain_detail_statistics AS
SELECT IFNULL(ic.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(like_count,0) AS like_count,target_date, s1.domain_id as domain_id FROM s1_cross_domain_detail_statistics s1
LEFT JOIN cross_domain_rolling_item_count_statistics ic
ON target_date=last_item_added_date and s1.domain_id=ic.domain_id
UNION ALL
SELECT IFNULL(ic.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(like_count,0) AS like_count,last_item_added_date as target_date, ic.domain_id as domain_id FROM s1_cross_domain_detail_statistics s1
RIGHT JOIN cross_domain_rolling_item_count_statistics ic
ON target_date=last_item_added_date and s1.domain_id=ic.domain_id;


DROP TABLE IF EXISTS s3_cross_domain_detail_statistics; 
CREATE TABLE s3_cross_domain_detail_statistics AS
SELECT distinct IFNULL(s2.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,target_date, s2.domain_id as domain_id FROM s2_cross_domain_detail_statistics s2
LEFT JOIN cross_domain_rolling_max_view_item_statistics mvi
ON target_date=last_item_view_date and s2.domain_id=mvi.domain_id
UNION ALL
SELECT distinct IFNULL(s2.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,last_item_view_date as target_date, mvi.domain_id as domain_id FROM s2_cross_domain_detail_statistics s2
RIGHT JOIN cross_domain_rolling_max_view_item_statistics mvi
ON target_date=last_item_view_date and s2.domain_id=mvi.domain_id;

DROP TABLE IF EXISTS s4_cross_domain_detail_statistics; 
CREATE TABLE s4_cross_domain_detail_statistics AS
SELECT distinct IFNULL(s3.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,IFNULL(max_item_like_count,0) as max_item_like_count,target_date, s3.domain_id as domain_id FROM s3_cross_domain_detail_statistics s3
LEFT JOIN cross_domain_rolling_max_like_item_statistics mli
ON target_date=last_item_like_date and s3.domain_id=mli.domain_id
UNION ALL
SELECT distinct IFNULL(s3.item_count,0) as item_count,IFNULL(view_count,0) AS view_count,IFNULL(max_item_view_count,0) as max_item_view_count,IFNULL(like_count,0) AS like_count,IFNULL(max_item_like_count,0) as max_item_like_count,last_item_like_date as target_date, mli.domain_id as domain_id FROM s3_cross_domain_detail_statistics s3
RIGHT JOIN cross_domain_rolling_max_like_item_statistics mli
ON target_date=last_item_like_date and s3.domain_id=mli.domain_id;

TRUNCATE TABLE cross_domain_detail_statistics;
INSERT cross_domain_detail_statistics
SELECT NULL, item_count,view_count,max_item_view_count as  view_count_max_by_item,like_count,max_item_like_count as  like_count_max_by_item,target_date as created,domain_id from s4_cross_domain_detail_statistics; 
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `insert_cross_domain_like_count_statistics`()
BEGIN
	TRUNCATE TABLE cross_domain_like_count_statistics;
	INSERT cross_domain_like_count_statistics
	SELECT NULL,SUM( like_value ) AS like_count, DATE( like_time ) AS like_date, domain_id
	FROM rayarcca_admin.member_item_like
	GROUP BY domain_id, DATE( like_time ) ;
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `insert_cross_domain_rolling_item_count_statistics`()
begin
DECLARE done INTEGER DEFAULT 0;
         DECLARE vlast_item_added_date DATE;
 DECLARE date_cursor CURSOR FOR SELECT distinct Date(item_date_added) as item_added_dates FROM rayarcca_admin.item where item_date_added is not null;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

CREATE TEMPORARY TABLE t_item_count (
	item_count INT,
    last_item_added_date DATE,
	domain_id tinyint unsigned NOT NULL
);

 OPEN date_cursor;
 get_maxdates: LOOP

 IF done = 1 THEN 
 LEAVE get_maxdates;
 END IF;
 
FETCH date_cursor INTO vlast_item_added_date;
INSERT t_item_count
SELECT item_count,last_item_date_added,domain_id
FROM
(
SELECT count(*) as item_count, Max(DATE(item_date_added)) as last_item_date_added,domain_id
FROM rayarcca_admin.item
WHERE item_status="PUBLISHED"
AND DATE(item_date_added)<= vlast_item_added_date
GROUP BY domain_id
) item_count_by_date;


END LOOP get_maxdates;
CLOSE date_cursor;
INSERT rayarcca_admin.cross_domain_rolling_item_count_statistics
SELECT NULL,item_count, last_item_added_date,domain_id
FROM (
SELECT distinct item_count, last_item_added_date,domain_id
FROM t_item_count ORDER BY domain_id,item_count
) distinct_item_count;

end$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `insert_cross_domain_rolling_max_like_item_statistics`()
begin
DECLARE done INTEGER DEFAULT 0;
         DECLARE last_like_date DATE;
 DECLARE date_cursor CURSOR FOR SELECT distinct Date(like_time) as item_like_dates FROM rayarcca_admin.member_item_like;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
TRUNCATE TABLE rayarcca_admin.cross_domain_rolling_max_like_item_statistics;
 OPEN date_cursor;
 get_maxdates: LOOP

 IF done = 1 THEN 
 LEAVE get_maxdates;
 END IF;
 
 FETCH date_cursor INTO last_like_date;
INSERT rayarcca_admin.cross_domain_rolling_max_like_item_statistics
SELECT NULL,lcid.item_id,lcid.like_count,last_like_date,lcid.domain_id
FROM
(
	SELECT item_id, SUM( like_value ) as like_count, last_like_date as last_like_time,domain_id
	FROM rayarcca_admin.member_item_like
	WHERE DATE( like_time ) <= last_like_date
	GROUP BY domain_id,item_id
) lcid
JOIN
(
	SELECT MAX(like_count) as like_count,domain_id
	FROM
	(
		SELECT item_id, SUM( like_value ) as like_count, last_like_date as last_like_time,domain_id
		FROM rayarcca_admin.member_item_like
		WHERE DATE( like_time ) <= last_like_date
		GROUP BY domain_id,item_id
	) like_count_by_item
	GROUP BY domain_id
)mlid
ON lcid.like_count=mlid.like_count
AND lcid.domain_id=mlid.domain_id;


END LOOP get_maxdates;
 
 CLOSE date_cursor;
end$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `insert_cross_domain_rolling_max_view_item_statistics`()
BEGIN
DECLARE done INTEGER DEFAULT 0;
         DECLARE last_view_date DATE;
 DECLARE date_cursor CURSOR FOR SELECT distinct Date(access_time) as item_view_dates FROM rayarcca_admin.member_item_access;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

TRUNCATE TABLE rayarcca_admin.cross_domain_rolling_max_view_item_statistics;

 OPEN date_cursor;
 get_maxdates: LOOP

 IF done = 1 THEN 
 LEAVE get_maxdates;
 END IF;
 
FETCH date_cursor INTO last_view_date;
INSERT rayarcca_admin.cross_domain_rolling_max_view_item_statistics
SELECT NULL,vcid.item_id,vcid.view_count,last_access_time,vcid.domain_id
FROM
(
SELECT item_id,count(ip_address) as view_count, last_view_date as last_access_time,domain_id
FROM rayarcca_admin.member_item_access
WHERE DATE(access_time)<= last_view_date
GROUP BY domain_id,item_id
) vcid
JOIN
(SELECT MAX(view_count) as view_count,domain_id
FROM
(
SELECT item_id,count(ip_address) as view_count, last_view_date as last_access_time,domain_id
FROM rayarcca_admin.member_item_access
WHERE DATE(access_time)<= last_view_date
GROUP BY domain_id,item_id
) view_count_by_item2
GROUP BY domain_id
)mvid
ON vcid.view_count=mvid.view_count
AND vcid.domain_id=mvid.domain_id;

END LOOP get_maxdates;
 
 CLOSE date_cursor;
end$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `insert_cross_domain_view_count_statistics`()
BEGIN
TRUNCATE TABLE cross_domain_view_count_statistics;
INSERT cross_domain_view_count_statistics
SELECT NULL,count(*) as view_count, view_date, domain_id
	FROM
	(
		SELECT dv.ip_address,dv.item_id,min(DATE(access_time)) as view_date,dv.domain_id
		FROM 
		(
			SELECT distinct ip_address, item_id,domain_id
			FROM rayarcca_admin.member_item_access
		) dv
		JOIN rayarcca_admin.member_item_access mia
		ON dv.ip_address=mia.ip_address and dv.item_id=mia.item_id and dv.domain_id=mia.domain_id
		GROUP BY dv.ip_address,dv.item_id,dv.domain_id
	) one_time_per_ip_view
	GROUP BY domain_id,view_date;
END$$

CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `load_calendar_dates`(dateStart DATE, dateEnd DATE)
BEGIN
  WHILE dateStart <= dateEnd DO
    INSERT INTO cross_domain_calendar (target_date) VALUES (dateStart);
    SET dateStart = date_add(dateStart, INTERVAL 1 DAY);
  END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `access_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `access_code` varchar(8) NOT NULL,
  `access_key` varchar(32) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`access_id`),
  UNIQUE KEY `access_code` (`access_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `access_session`
--

CREATE TABLE IF NOT EXISTS `access_session` (
  `access_id` smallint(5) unsigned NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`access_id`,`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `account_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `account_key` varchar(8) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `client1_contact`
--

CREATE TABLE IF NOT EXISTS `client1_contact` (
  `client_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `company_size` varchar(32) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `company_website` varchar(128) DEFAULT NULL,
  `status` varchar(16) NOT NULL,
  `role` tinyint(3) unsigned DEFAULT NULL,
  `plan_id` tinyint(3) unsigned DEFAULT '0',
  `verify_key` varchar(16) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Table structure for table `client1_contact_temp`
--

CREATE TABLE IF NOT EXISTS `client1_contact_temp` (
  `client_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `company_size` varchar(32) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `company_website` varchar(128) DEFAULT NULL,
  `status` varchar(16) NOT NULL,
  `role` tinyint(3) unsigned DEFAULT NULL,
  `plan_id` tinyint(3) unsigned DEFAULT '0',
  `verify_key` varchar(16) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=88 ;

-- --------------------------------------------------------

--
-- Table structure for table `client2_location`
--

CREATE TABLE IF NOT EXISTS `client2_location` (
  `client_id` tinyint(3) unsigned NOT NULL,
  `company_website` varchar(128) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `postal_code` varchar(7) DEFAULT NULL,
  `mc_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  KEY `mc_id` (`mc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client2_location_temp`
--

CREATE TABLE IF NOT EXISTS `client2_location_temp` (
  `client_id` tinyint(3) unsigned NOT NULL,
  `country` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `postal_code` varchar(7) DEFAULT NULL,
  `mc_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  KEY `mc_id` (`mc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client3_account`
--

CREATE TABLE IF NOT EXISTS `client3_account` (
  `client_id` tinyint(3) unsigned NOT NULL,
  `subdomain_name` varchar(32) DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `subdomain_name` (`subdomain_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client3_account_temp`
--

CREATE TABLE IF NOT EXISTS `client3_account_temp` (
  `client_id` tinyint(3) unsigned NOT NULL,
  `subdomain_name` varchar(32) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_member`
--

CREATE TABLE IF NOT EXISTS `client_member` (
  `client_id` tinyint(3) unsigned NOT NULL,
  `member_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`client_id`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_calendar`
--

CREATE TABLE IF NOT EXISTS `cross_domain_calendar` (
  `target_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_detail_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_detail_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_count` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_max_by_item` int(10) unsigned NOT NULL DEFAULT '0',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0',
  `like_count_max_by_item` int(10) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_like_count_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_like_count_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `like_count` int(11) DEFAULT NULL,
  `like_date` date DEFAULT NULL,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_rolling_item_count_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_rolling_item_count_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_count` int(11) DEFAULT NULL,
  `last_item_added_date` date DEFAULT NULL,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_rolling_max_like_item_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_rolling_max_like_item_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(6) DEFAULT NULL,
  `max_item_like_count` int(11) DEFAULT NULL,
  `last_item_like_date` date DEFAULT NULL,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_rolling_max_view_item_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_rolling_max_view_item_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(6) DEFAULT NULL,
  `max_item_view_count` int(11) DEFAULT NULL,
  `last_item_view_date` date DEFAULT NULL,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=78 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_summary_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_summary_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_count` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count` int(10) unsigned NOT NULL DEFAULT '0',
  `view_count_max_by_item` int(10) unsigned NOT NULL DEFAULT '0',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0',
  `like_count_max_by_item` int(10) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stats_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_view_count_statistics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_view_count_statistics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `view_count` int(11) DEFAULT NULL,
  `view_date` date DEFAULT NULL,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `cross_domain_web_summary_analytics`
--

CREATE TABLE IF NOT EXISTS `cross_domain_web_summary_analytics` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users` int(10) unsigned NOT NULL DEFAULT '0',
  `sessions` int(10) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stats_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `date_range`
--
CREATE TABLE IF NOT EXISTS `date_range` (
`target_date` date
);
-- --------------------------------------------------------

--
-- Table structure for table `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `domain_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(64) DEFAULT NULL,
  `domain_title` varchar(64) DEFAULT NULL,
  `domain_short_desc` varchar(256) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `plan_id` tinyint(3) unsigned NOT NULL,
  `domain_parent_id` tinyint(3) DEFAULT NULL,
  `client_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`domain_id`),
  UNIQUE KEY `domain_name` (`domain_name`),
  KEY `client_id` (`client_id`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `domain_account`
--

CREATE TABLE IF NOT EXISTS `domain_account` (
  `domain_id` tinyint(3) unsigned NOT NULL,
  `account_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`domain_id`,`account_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domain_member`
--

CREATE TABLE IF NOT EXISTS `domain_member` (
  `domain_id` tinyint(3) unsigned NOT NULL,
  `member_id` smallint(5) unsigned NOT NULL,
  `admin_flag` tinyint(3) unsigned DEFAULT NULL,
  `client_flag` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domain_member_temp`
--

CREATE TABLE IF NOT EXISTS `domain_member_temp` (
  `domain_id` tinyint(3) unsigned NOT NULL,
  `member_id` smallint(5) unsigned NOT NULL,
  `admin_flag` tinyint(3) unsigned DEFAULT NULL,
  `client_flag` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domain_profile`
--

CREATE TABLE IF NOT EXISTS `domain_profile` (
  `domain_id` tinyint(3) unsigned NOT NULL,
  `profile_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`domain_id`,`profile_id`),
  KEY `profile_id` (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eblast_master`
--

CREATE TABLE IF NOT EXISTS `eblast_master` (
  `eblast_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(1) DEFAULT NULL,
  `eblast_name` varchar(64) NOT NULL,
  `target_page` varchar(255) NOT NULL,
  `from_name` varchar(128) NOT NULL,
  `from_email` varchar(128) NOT NULL,
  `to_emails` varchar(128) NOT NULL,
  `to_names` varchar(128) DEFAULT NULL,
  `subject` varchar(128) NOT NULL,
  `message` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eblast_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=88 ;

-- --------------------------------------------------------

--
-- Table structure for table `eblast_master2`
--

CREATE TABLE IF NOT EXISTS `eblast_master2` (
  `eblast_id` int(10) unsigned NOT NULL,
  `domain_id` tinyint(3) unsigned DEFAULT NULL,
  `profile_id` smallint(5) unsigned DEFAULT NULL,
  `item_id` smallint(5) unsigned DEFAULT NULL,
  `unique_id_referer` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`eblast_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eblast_requests`
--

CREATE TABLE IF NOT EXISTS `eblast_requests` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `Name` varchar(64) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eblast_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`request_id`),
  UNIQUE KEY `unique_id` (`unique_id`),
  KEY `eblast_id` (`eblast_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=139 ;

-- --------------------------------------------------------

--
-- Table structure for table `eblast_responses`
--

CREATE TABLE IF NOT EXISTS `eblast_responses` (
  `response_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) NOT NULL,
  `ipAddress` varchar(32) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_id` int(10) unsigned NOT NULL,
  `type` char(1) DEFAULT 'C',
  PRIMARY KEY (`response_id`),
  KEY `request_id` (`request_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=510 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `event`
--
CREATE TABLE IF NOT EXISTS `event` (
`event_id` smallint(6) unsigned
,`event_key` varchar(8)
,`event_name` varchar(64)
,`event_short_name` varchar(32)
,`event_desc` varchar(256)
,`event_image_path` varchar(255)
,`event_tn_path` varchar(255)
,`event_itinerary` varchar(256)
,`event_details` varchar(256)
,`event_start_datetime` datetime
,`event_end_datetime` datetime
,`event_reoccurring` tinyint(4)
,`event_every` varchar(16)
,`starting_on` date
,`event_coordinator` varchar(64)
,`event_performances_by` varchar(512)
,`event_hosted_by` varchar(256)
,`event_in_support_of` varchar(64)
,`event_contact_email` varchar(128)
,`event_contact_number1` varchar(32)
,`event_contact_number2` varchar(32)
,`venue_id` smallint(6) unsigned
,`domain_id` varchar(1)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `event_item`
--
CREATE TABLE IF NOT EXISTS `event_item` (
`event_id` smallint(6) unsigned
,`item_id` smallint(6) unsigned
,`domain_id` varchar(1)
);
-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `genre_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `genre_name` varchar(32) NOT NULL,
  `genre_title` varchar(32) NOT NULL,
  `parent_genre_id` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`genre_id`),
  UNIQUE KEY `genre_name` (`genre_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `item`
--
CREATE TABLE IF NOT EXISTS `item` (
`item_id` smallint(6) unsigned
,`item_key` varchar(8)
,`item_title` varchar(128)
,`item_desc` varchar(256)
,`item_long_desc` text
,`item_image` varchar(255)
,`item_duration` smallint(6) unsigned
,`item_content_source` varchar(255)
,`item_download_source` varchar(255)
,`item_status` varchar(16)
,`item_access` char(1)
,`item_on_feature` tinyint(4)
,`item_downloadable` tinyint(4)
,`item_date_added` timestamp
,`item_expires` timestamp
,`item_parent_id` smallint(6) unsigned
,`profile_id` smallint(6) unsigned
,`item_category_id` tinyint(4) unsigned
,`item_content_type_id` char(1)
,`item_content_provider_id` char(2)
,`item_tag` varchar(256)
,`points` bigint(20)
,`domain_id` varchar(2)
);
-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE IF NOT EXISTS `item_category` (
  `ic_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `ic_name` varchar(32) NOT NULL,
  `ic_title` varchar(32) NOT NULL,
  PRIMARY KEY (`ic_id`),
  UNIQUE KEY `ic_name` (`ic_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `item_content_provider`
--

CREATE TABLE IF NOT EXISTS `item_content_provider` (
  `provider_id` char(2) NOT NULL,
  `provider_name` varchar(64) NOT NULL,
  PRIMARY KEY (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_featured_stack`
--

CREATE TABLE IF NOT EXISTS `item_featured_stack` (
  `ifs_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(6) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `domain_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`ifs_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `item_genre`
--
CREATE TABLE IF NOT EXISTS `item_genre` (
`item_id` smallint(6) unsigned
,`genre_id` tinyint(4) unsigned
,`domain_id` varchar(1)
);
-- --------------------------------------------------------

--
-- Table structure for table `item_premium`
--

CREATE TABLE IF NOT EXISTS `item_premium` (
  `premium_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` tinyint(3) unsigned NOT NULL,
  `item_id` smallint(6) DEFAULT NULL,
  `view_count` int(10) unsigned NOT NULL DEFAULT '0',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0',
  `rating` int(10) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`premium_item_id`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `item_stats`
--

CREATE TABLE IF NOT EXISTS `item_stats` (
  `stat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `processing_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avg_item_sum_like_value` mediumint(8) unsigned NOT NULL,
  `max_item_sum_like_value` mediumint(8) unsigned NOT NULL,
  `avg_item_view_start_by_ip_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_start_by_ip_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_start_by_session_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_start_by_session_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_start_all_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_start_all_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_end_by_ip_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_end_by_ip_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_end_by_session_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_end_by_session_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_end_all_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_end_all_count` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE IF NOT EXISTS `job` (
  `job_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(128) NOT NULL,
  `sp_filename` varchar(128) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_runtime_history`
--

CREATE TABLE IF NOT EXISTS `job_runtime_history` (
  `job_id` tinyint(3) unsigned NOT NULL,
  `runtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_id`,`runtime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `list_item`
--
CREATE TABLE IF NOT EXISTS `list_item` (
`list_id` smallint(6) unsigned
,`list_type_id` tinyint(4) unsigned
,`item_id` smallint(6) unsigned
,`list_item_date_added` timestamp
,`domainID` varchar(2)
);
-- --------------------------------------------------------

--
-- Table structure for table `list_type`
--

CREATE TABLE IF NOT EXISTS `list_type` (
  `list_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `list_type_name` varchar(64) NOT NULL,
  PRIMARY KEY (`list_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `market_channel`
--

CREATE TABLE IF NOT EXISTS `market_channel` (
  `mc_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `mc_name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`mc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE IF NOT EXISTS `member` (
  `member_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `member_key` varchar(8) DEFAULT NULL,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `status` varchar(16) NOT NULL,
  `role_id` tinyint(3) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `private_flag` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `member_key` (`member_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=457 ;

-- --------------------------------------------------------

--
-- Table structure for table `member1_contact`
--

CREATE TABLE IF NOT EXISTS `member1_contact` (
  `member_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `status` varchar(16) NOT NULL,
  `verify_key` varchar(16) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `member2`
--

CREATE TABLE IF NOT EXISTS `member2` (
  `member_id` smallint(5) unsigned NOT NULL,
  `country` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `postal_code` varchar(7) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `mc_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  KEY `mc_id` (`mc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member2_legacy`
--

CREATE TABLE IF NOT EXISTS `member2_legacy` (
  `member_id` int(10) unsigned NOT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `website` varchar(128) DEFAULT NULL,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `state` varchar(32) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile_number` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member2_temp`
--

CREATE TABLE IF NOT EXISTS `member2_temp` (
  `member_id` smallint(5) unsigned NOT NULL,
  `country` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `postal_code` varchar(7) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `mc_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  KEY `mc_id` (`mc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member3`
--

CREATE TABLE IF NOT EXISTS `member3` (
  `member_id` smallint(5) unsigned NOT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `website` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member3_temp`
--

CREATE TABLE IF NOT EXISTS `member3_temp` (
  `member_id` smallint(5) unsigned NOT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `website` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `member_item_access`
--
CREATE TABLE IF NOT EXISTS `member_item_access` (
`access_id` mediumint(8) unsigned
,`item_id` smallint(6) unsigned
,`access_type` char(1)
,`access_time` timestamp
,`session_id` varchar(64)
,`ip_address` varchar(32)
,`session_member_id` smallint(6) unsigned
,`domain_id` bigint(20)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `member_item_like`
--
CREATE TABLE IF NOT EXISTS `member_item_like` (
`like_id` mediumint(8) unsigned
,`item_id` smallint(6) unsigned
,`like_value` tinyint(4)
,`like_time` timestamp
,`session_id` varchar(64)
,`ip_address` varchar(32)
,`session_member_id` smallint(6) unsigned
,`domain_id` bigint(20)
);
-- --------------------------------------------------------

--
-- Table structure for table `member_legacy`
--

CREATE TABLE IF NOT EXISTS `member_legacy` (
  `member_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `security_key` varchar(64) DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `status` varchar(16) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `role_access_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`member_id`),
  KEY `role_access_id` (`role_access_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=196 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_social_login`
--

CREATE TABLE IF NOT EXISTS `member_social_login` (
  `social_provider_id` char(2) NOT NULL,
  `social_user_id` varchar(64) NOT NULL DEFAULT '',
  `social_user_name` varchar(64) DEFAULT NULL,
  `social_access_token` varchar(64) DEFAULT NULL,
  `member_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`social_provider_id`,`social_user_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member_temp`
--

CREATE TABLE IF NOT EXISTS `member_temp` (
  `member_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `member_key` varchar(8) DEFAULT NULL,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) DEFAULT NULL,
  `status` varchar(16) NOT NULL,
  `role_id` tinyint(3) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=210 ;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE IF NOT EXISTS `package` (
  `package_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `package_name` varchar(16) NOT NULL,
  `monthly_fee` decimal(4,2) NOT NULL,
  `contributor_count_limit` tinyint(1) unsigned NOT NULL,
  `contributor_content_count_limit` tinyint(1) unsigned NOT NULL,
  `use_own_url` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `plan`
--

CREATE TABLE IF NOT EXISTS `plan` (
  `plan_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(16) NOT NULL,
  `monthly_fee` decimal(4,2) NOT NULL,
  `contributor_count_limit` tinyint(1) unsigned DEFAULT NULL,
  `contributor_submission_count_limit` tinyint(1) DEFAULT NULL,
  `has_profile_site` tinyint(1) unsigned NOT NULL,
  `has_portal_site` tinyint(1) unsigned NOT NULL,
  `use_own_url` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `points_legend`
--

CREATE TABLE IF NOT EXISTS `points_legend` (
  `pl_name` varchar(32) NOT NULL,
  `pl_value` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`pl_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE IF NOT EXISTS `profile` (
  `profile_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `profile_key` varchar(8) NOT NULL,
  `profile_name` varchar(32) NOT NULL,
  `short_bio` varchar(2500) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `member_id` smallint(5) unsigned NOT NULL,
  `role_id` tinyint(3) unsigned DEFAULT NULL,
  `points` bigint(20) DEFAULT '0',
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `profile_name` (`profile_name`),
  KEY `member_id` (`member_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=242 ;

-- --------------------------------------------------------

--
-- Table structure for table `profile_legacy`
--

CREATE TABLE IF NOT EXISTS `profile_legacy` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(16) DEFAULT NULL,
  `profile_name` varchar(32) NOT NULL,
  `description` text,
  `status` varchar(16) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `member_id` int(10) unsigned NOT NULL,
  `role_access_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`profile_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=99 ;

-- --------------------------------------------------------

--
-- Table structure for table `prospect`
--

CREATE TABLE IF NOT EXISTS `prospect` (
  `prospect_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `role_id` tinyint(3) unsigned NOT NULL,
  `plan_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `company_name` varchar(64) DEFAULT NULL,
  `website` varchar(128) DEFAULT NULL,
  `contact_number` varchar(32) DEFAULT NULL,
  `mc_id` tinyint(3) unsigned DEFAULT NULL,
  `project` text,
  `status` varchar(16) DEFAULT NULL,
  `verify_key` varchar(16) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prospect_id`),
  KEY `role_id` (`role_id`),
  KEY `mc_id` (`mc_id`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `prospect_service`
--

CREATE TABLE IF NOT EXISTS `prospect_service` (
  `prospect_id` tinyint(3) unsigned NOT NULL,
  `service_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`prospect_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `role_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(32) NOT NULL DEFAULT 'fan',
  `role_title` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `role_child_link`
--

CREATE TABLE IF NOT EXISTS `role_child_link` (
  `role_id` tinyint(3) unsigned NOT NULL,
  `role_child_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`,`role_child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `role_family`
--

CREATE TABLE IF NOT EXISTS `role_family` (
  `role_family_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `role_parent_id` tinyint(3) unsigned DEFAULT NULL,
  `role_id` tinyint(3) unsigned NOT NULL,
  `role_category` varchar(12) NOT NULL,
  PRIMARY KEY (`role_family_id`),
  UNIQUE KEY `role_pair_key` (`role_parent_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `s1_cross_domain_detail_statistics`
--

CREATE TABLE IF NOT EXISTS `s1_cross_domain_detail_statistics` (
  `view_count` bigint(11) DEFAULT NULL,
  `like_count` bigint(11) DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `domain_id` tinyint(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `s2_cross_domain_detail_statistics`
--

CREATE TABLE IF NOT EXISTS `s2_cross_domain_detail_statistics` (
  `item_count` bigint(20) NOT NULL DEFAULT '0',
  `view_count` bigint(20) NOT NULL DEFAULT '0',
  `like_count` bigint(20) NOT NULL DEFAULT '0',
  `target_date` date DEFAULT NULL,
  `domain_id` tinyint(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `s3_cross_domain_detail_statistics`
--

CREATE TABLE IF NOT EXISTS `s3_cross_domain_detail_statistics` (
  `item_count` bigint(20) NOT NULL DEFAULT '0',
  `view_count` bigint(20) NOT NULL DEFAULT '0',
  `max_item_view_count` bigint(20) NOT NULL DEFAULT '0',
  `like_count` bigint(20) NOT NULL DEFAULT '0',
  `target_date` date DEFAULT NULL,
  `domain_id` tinyint(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `s4_cross_domain_detail_statistics`
--

CREATE TABLE IF NOT EXISTS `s4_cross_domain_detail_statistics` (
  `item_count` bigint(20) NOT NULL DEFAULT '0',
  `view_count` bigint(20) NOT NULL DEFAULT '0',
  `max_item_view_count` bigint(20) NOT NULL DEFAULT '0',
  `like_count` bigint(20) NOT NULL DEFAULT '0',
  `max_item_like_count` bigint(20) NOT NULL DEFAULT '0',
  `target_date` date DEFAULT NULL,
  `domain_id` tinyint(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `service_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(8) NOT NULL,
  `service_title` varchar(32) NOT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscribable`
--

CREATE TABLE IF NOT EXISTS `subscribable` (
  `subscribable_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `tag` varchar(128) NOT NULL,
  PRIMARY KEY (`subscribable_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscriber`
--

CREATE TABLE IF NOT EXISTS `subscriber` (
  `subscriber_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `status` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `firstname` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `member_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=94 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscriber_access`
--

CREATE TABLE IF NOT EXISTS `subscriber_access` (
  `subscriber_id` smallint(5) unsigned NOT NULL,
  `access_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`subscriber_id`),
  KEY `access_id` (`access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE IF NOT EXISTS `subscription` (
  `subscription_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) NOT NULL,
  `subscribable_id` smallint(5) unsigned NOT NULL,
  `subscriber_id` smallint(5) unsigned NOT NULL,
  `status` char(1) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `domain_id` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY `unique_id` (`unique_id`),
  UNIQUE KEY `sub_ids` (`subscribable_id`,`subscriber_id`,`domain_id`),
  KEY `subscriber_id` (`subscriber_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE IF NOT EXISTS `venue` (
  `venue_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `venue_name` varchar(64) NOT NULL,
  `venue_type_id` tinyint(3) unsigned NOT NULL,
  `venue_addr` varchar(128) DEFAULT NULL,
  `venue_city` varchar(32) DEFAULT NULL,
  `venue_state` varchar(32) DEFAULT NULL,
  `venue_country` varchar(32) DEFAULT NULL,
  `venue_zipcode` varchar(8) DEFAULT NULL,
  `venue_email` varchar(32) DEFAULT NULL,
  `venue_contact_name` varchar(32) DEFAULT NULL,
  `venue_contact_phone` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`venue_id`),
  KEY `venue_type_id` (`venue_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `venue_type`
--

CREATE TABLE IF NOT EXISTS `venue_type` (
  `venue_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `venue_type_name` varchar(32) NOT NULL,
  PRIMARY KEY (`venue_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Structure for view `date_range`
--
DROP TABLE IF EXISTS `date_range`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `date_range` AS select `cross_domain_calendar`.`target_date` AS `target_date` from `cross_domain_calendar` where ((cast(`cross_domain_calendar`.`target_date` as date) >= '05-12-2012') and (cast(`cross_domain_calendar`.`target_date` as date) < cast(now() as date)));

-- --------------------------------------------------------

--
-- Structure for view `event`
--
DROP TABLE IF EXISTS `event`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `event` AS select `rayarcca_music`.`event`.`event_id` AS `event_id`,`rayarcca_music`.`event`.`event_key` AS `event_key`,`rayarcca_music`.`event`.`event_name` AS `event_name`,`rayarcca_music`.`event`.`event_short_name` AS `event_short_name`,`rayarcca_music`.`event`.`event_desc` AS `event_desc`,`rayarcca_music`.`event`.`event_image_path` AS `event_image_path`,`rayarcca_music`.`event`.`event_tn_path` AS `event_tn_path`,`rayarcca_music`.`event`.`event_itinerary` AS `event_itinerary`,`rayarcca_music`.`event`.`event_details` AS `event_details`,`rayarcca_music`.`event`.`event_start_datetime` AS `event_start_datetime`,`rayarcca_music`.`event`.`event_end_datetime` AS `event_end_datetime`,`rayarcca_music`.`event`.`event_reoccurring` AS `event_reoccurring`,`rayarcca_music`.`event`.`event_every` AS `event_every`,`rayarcca_music`.`event`.`starting_on` AS `starting_on`,`rayarcca_music`.`event`.`event_coordinator` AS `event_coordinator`,`rayarcca_music`.`event`.`event_performances_by` AS `event_performances_by`,`rayarcca_music`.`event`.`event_hosted_by` AS `event_hosted_by`,`rayarcca_music`.`event`.`event_in_support_of` AS `event_in_support_of`,`rayarcca_music`.`event`.`event_contact_email` AS `event_contact_email`,`rayarcca_music`.`event`.`event_contact_number1` AS `event_contact_number1`,`rayarcca_music`.`event`.`event_contact_number2` AS `event_contact_number2`,`rayarcca_music`.`event`.`venue_id` AS `venue_id`,'5' AS `domain_id` from `rayarcca_music`.`event` union select `rayarcca_yeidol`.`event`.`event_id` AS `event_id`,`rayarcca_yeidol`.`event`.`event_key` AS `event_key`,`rayarcca_yeidol`.`event`.`event_name` AS `event_name`,`rayarcca_yeidol`.`event`.`event_short_name` AS `event_short_name`,`rayarcca_yeidol`.`event`.`event_desc` AS `event_desc`,`rayarcca_yeidol`.`event`.`event_image_path` AS `event_image_path`,`rayarcca_yeidol`.`event`.`event_tn_path` AS `event_tn_path`,`rayarcca_yeidol`.`event`.`event_itinerary` AS `event_itinerary`,`rayarcca_yeidol`.`event`.`event_details` AS `event_details`,`rayarcca_yeidol`.`event`.`event_start_datetime` AS `event_start_datetime`,`rayarcca_yeidol`.`event`.`event_end_datetime` AS `event_end_datetime`,`rayarcca_yeidol`.`event`.`event_reoccurring` AS `event_reoccurring`,`rayarcca_yeidol`.`event`.`event_every` AS `event_every`,`rayarcca_yeidol`.`event`.`starting_on` AS `starting_on`,`rayarcca_yeidol`.`event`.`event_coordinator` AS `event_coordinator`,`rayarcca_yeidol`.`event`.`event_performances_by` AS `event_performances_by`,`rayarcca_yeidol`.`event`.`event_hosted_by` AS `event_hosted_by`,`rayarcca_yeidol`.`event`.`event_in_support_of` AS `event_in_support_of`,`rayarcca_yeidol`.`event`.`event_contact_email` AS `event_contact_email`,`rayarcca_yeidol`.`event`.`event_contact_number1` AS `event_contact_number1`,`rayarcca_yeidol`.`event`.`event_contact_number2` AS `event_contact_number2`,`rayarcca_yeidol`.`event`.`venue_id` AS `venue_id`,'6' AS `domain_id` from `rayarcca_yeidol`.`event` union select `rayarcca_nashelsevent`.`event`.`event_id` AS `event_id`,`rayarcca_nashelsevent`.`event`.`event_key` AS `event_key`,`rayarcca_nashelsevent`.`event`.`event_name` AS `event_name`,`rayarcca_nashelsevent`.`event`.`event_short_name` AS `event_short_name`,`rayarcca_nashelsevent`.`event`.`event_desc` AS `event_desc`,`rayarcca_nashelsevent`.`event`.`event_image_path` AS `event_image_path`,`rayarcca_nashelsevent`.`event`.`event_tn_path` AS `event_tn_path`,`rayarcca_nashelsevent`.`event`.`event_itinerary` AS `event_itinerary`,`rayarcca_nashelsevent`.`event`.`event_details` AS `event_details`,`rayarcca_nashelsevent`.`event`.`event_start_datetime` AS `event_start_datetime`,`rayarcca_nashelsevent`.`event`.`event_end_datetime` AS `event_end_datetime`,`rayarcca_nashelsevent`.`event`.`event_reoccurring` AS `event_reoccurring`,`rayarcca_nashelsevent`.`event`.`event_every` AS `event_every`,`rayarcca_nashelsevent`.`event`.`starting_on` AS `starting_on`,`rayarcca_nashelsevent`.`event`.`event_coordinator` AS `event_coordinator`,`rayarcca_nashelsevent`.`event`.`event_performances_by` AS `event_performances_by`,`rayarcca_nashelsevent`.`event`.`event_hosted_by` AS `event_hosted_by`,`rayarcca_nashelsevent`.`event`.`event_in_support_of` AS `event_in_support_of`,`rayarcca_nashelsevent`.`event`.`event_contact_email` AS `event_contact_email`,`rayarcca_nashelsevent`.`event`.`event_contact_number1` AS `event_contact_number1`,`rayarcca_nashelsevent`.`event`.`event_contact_number2` AS `event_contact_number2`,`rayarcca_nashelsevent`.`event`.`venue_id` AS `venue_id`,'7' AS `domain_id` from `rayarcca_nashelsevent`.`event`;

-- --------------------------------------------------------

--
-- Structure for view `event_item`
--
DROP TABLE IF EXISTS `event_item`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `event_item` AS select `rayarcca_music`.`event_item`.`event_id` AS `event_id`,`rayarcca_music`.`event_item`.`item_id` AS `item_id`,'5' AS `domain_id` from `rayarcca_music`.`event_item` union select `rayarcca_yeidol`.`event_item`.`event_id` AS `event_id`,`rayarcca_yeidol`.`event_item`.`item_id` AS `item_id`,'6' AS `domain_id` from `rayarcca_yeidol`.`event_item` union select `rayarcca_nashelsevent`.`event_item`.`event_id` AS `event_id`,`rayarcca_nashelsevent`.`event_item`.`item_id` AS `item_id`,'7' AS `domain_id` from `rayarcca_nashelsevent`.`event_item`;

-- --------------------------------------------------------

--
-- Structure for view `item`
--
DROP TABLE IF EXISTS `item`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `item` AS select `rayarcca_music`.`item`.`item_id` AS `item_id`,`rayarcca_music`.`item`.`item_key` AS `item_key`,`rayarcca_music`.`item`.`item_title` AS `item_title`,`rayarcca_music`.`item`.`item_desc` AS `item_desc`,`rayarcca_music`.`item`.`item_long_desc` AS `item_long_desc`,`rayarcca_music`.`item`.`item_image` AS `item_image`,`rayarcca_music`.`item`.`item_duration` AS `item_duration`,`rayarcca_music`.`item`.`item_content_source` AS `item_content_source`,`rayarcca_music`.`item`.`item_download_source` AS `item_download_source`,`rayarcca_music`.`item`.`item_status` AS `item_status`,`rayarcca_music`.`item`.`item_access` AS `item_access`,`rayarcca_music`.`item`.`item_on_feature` AS `item_on_feature`,`rayarcca_music`.`item`.`item_downloadable` AS `item_downloadable`,`rayarcca_music`.`item`.`item_date_added` AS `item_date_added`,`rayarcca_music`.`item`.`item_expires` AS `item_expires`,`rayarcca_music`.`item`.`item_parent_id` AS `item_parent_id`,`rayarcca_music`.`item`.`profile_id` AS `profile_id`,`rayarcca_music`.`item`.`item_category_id` AS `item_category_id`,`rayarcca_music`.`item`.`item_content_type_id` AS `item_content_type_id`,`rayarcca_music`.`item`.`item_content_provider_id` AS `item_content_provider_id`,`rayarcca_music`.`item`.`item_tag` AS `item_tag`,`rayarcca_music`.`item`.`points` AS `points`,'5' AS `domain_id` from `rayarcca_music`.`item` union select `rayarcca_yeidol`.`item`.`item_id` AS `item_id`,`rayarcca_yeidol`.`item`.`item_key` AS `item_key`,`rayarcca_yeidol`.`item`.`item_title` AS `item_title`,`rayarcca_yeidol`.`item`.`item_desc` AS `item_desc`,`rayarcca_yeidol`.`item`.`item_long_desc` AS `item_long_desc`,`rayarcca_yeidol`.`item`.`item_image` AS `item_image`,`rayarcca_yeidol`.`item`.`item_duration` AS `item_duration`,`rayarcca_yeidol`.`item`.`item_content_source` AS `item_content_source`,`rayarcca_yeidol`.`item`.`item_download_source` AS `item_download_source`,`rayarcca_yeidol`.`item`.`item_status` AS `item_status`,`rayarcca_yeidol`.`item`.`item_access` AS `item_access`,`rayarcca_yeidol`.`item`.`item_on_feature` AS `item_on_feature`,`rayarcca_yeidol`.`item`.`item_downloadable` AS `item_downloadable`,`rayarcca_yeidol`.`item`.`item_date_added` AS `item_date_added`,`rayarcca_yeidol`.`item`.`item_expires` AS `item_expires`,`rayarcca_yeidol`.`item`.`item_parent_id` AS `item_parent_id`,`rayarcca_yeidol`.`item`.`profile_id` AS `profile_id`,`rayarcca_yeidol`.`item`.`item_category_id` AS `item_category_id`,`rayarcca_yeidol`.`item`.`item_content_type_id` AS `item_content_type_id`,`rayarcca_yeidol`.`item`.`item_content_provider_id` AS `item_content_provider_id`,`rayarcca_yeidol`.`item`.`item_tag` AS `item_tag`,`rayarcca_yeidol`.`item`.`points` AS `points`,'6' AS `domain_id` from `rayarcca_yeidol`.`item` union select `rayarcca_nashelsevent`.`item`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`item`.`item_key` AS `item_key`,`rayarcca_nashelsevent`.`item`.`item_title` AS `item_title`,`rayarcca_nashelsevent`.`item`.`item_desc` AS `item_desc`,`rayarcca_nashelsevent`.`item`.`item_long_desc` AS `item_long_desc`,`rayarcca_nashelsevent`.`item`.`item_image` AS `item_image`,`rayarcca_nashelsevent`.`item`.`item_duration` AS `item_duration`,`rayarcca_nashelsevent`.`item`.`item_content_source` AS `item_content_source`,`rayarcca_nashelsevent`.`item`.`item_download_source` AS `item_download_source`,`rayarcca_nashelsevent`.`item`.`item_status` AS `item_status`,`rayarcca_nashelsevent`.`item`.`item_access` AS `item_access`,`rayarcca_nashelsevent`.`item`.`item_on_feature` AS `item_on_feature`,`rayarcca_nashelsevent`.`item`.`item_downloadable` AS `item_downloadable`,`rayarcca_nashelsevent`.`item`.`item_date_added` AS `item_date_added`,`rayarcca_nashelsevent`.`item`.`item_expires` AS `item_expires`,`rayarcca_nashelsevent`.`item`.`item_parent_id` AS `item_parent_id`,`rayarcca_nashelsevent`.`item`.`profile_id` AS `profile_id`,`rayarcca_nashelsevent`.`item`.`item_category_id` AS `item_category_id`,`rayarcca_nashelsevent`.`item`.`item_content_type_id` AS `item_content_type_id`,`rayarcca_nashelsevent`.`item`.`item_content_provider_id` AS `item_content_provider_id`,`rayarcca_nashelsevent`.`item`.`item_tag` AS `item_tag`,`rayarcca_nashelsevent`.`item`.`points` AS `points`,'7' AS `domain_id` from `rayarcca_nashelsevent`.`item` union select `rayarcca_productions`.`item`.`item_id` AS `item_id`,`rayarcca_productions`.`item`.`item_key` AS `item_key`,`rayarcca_productions`.`item`.`item_title` AS `item_title`,`rayarcca_productions`.`item`.`item_desc` AS `item_desc`,`rayarcca_productions`.`item`.`item_long_desc` AS `item_long_desc`,`rayarcca_productions`.`item`.`item_image` AS `item_image`,`rayarcca_productions`.`item`.`item_duration` AS `item_duration`,`rayarcca_productions`.`item`.`item_content_source` AS `item_content_source`,`rayarcca_productions`.`item`.`item_download_source` AS `item_download_source`,`rayarcca_productions`.`item`.`item_status` AS `item_status`,`rayarcca_productions`.`item`.`item_access` AS `item_access`,`rayarcca_productions`.`item`.`item_on_feature` AS `item_on_feature`,`rayarcca_productions`.`item`.`item_downloadable` AS `item_downloadable`,`rayarcca_productions`.`item`.`item_date_added` AS `item_date_added`,`rayarcca_productions`.`item`.`item_expires` AS `item_expires`,`rayarcca_productions`.`item`.`item_parent_id` AS `item_parent_id`,`rayarcca_productions`.`item`.`profile_id` AS `profile_id`,`rayarcca_productions`.`item`.`item_category_id` AS `item_category_id`,`rayarcca_productions`.`item`.`item_content_type_id` AS `item_content_type_id`,`rayarcca_productions`.`item`.`item_content_provider_id` AS `item_content_provider_id`,`rayarcca_productions`.`item`.`item_tag` AS `item_tag`,`rayarcca_productions`.`item`.`points` AS `points`,'14' AS `domain_id` from `rayarcca_productions`.`item`;

-- --------------------------------------------------------

--
-- Structure for view `item_genre`
--
DROP TABLE IF EXISTS `item_genre`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `item_genre` AS select `rayarcca_music`.`item_genre`.`item_id` AS `item_id`,`rayarcca_music`.`item_genre`.`genre_id` AS `genre_id`,'5' AS `domain_id` from `rayarcca_music`.`item_genre` union select `rayarcca_yeidol`.`item_genre`.`item_id` AS `item_id`,`rayarcca_yeidol`.`item_genre`.`genre_id` AS `genre_id`,'6' AS `domain_id` from `rayarcca_yeidol`.`item_genre` union select `rayarcca_nashelsevent`.`item_genre`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`item_genre`.`genre_id` AS `genre_id`,'7' AS `domain_id` from `rayarcca_nashelsevent`.`item_genre`;

-- --------------------------------------------------------

--
-- Structure for view `list_item`
--
DROP TABLE IF EXISTS `list_item`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `list_item` AS select `rayarcca_cloud`.`list_item`.`list_id` AS `list_id`,`rayarcca_cloud`.`list_item`.`list_type_id` AS `list_type_id`,`rayarcca_cloud`.`list_item`.`item_id` AS `item_id`,`rayarcca_cloud`.`list_item`.`list_item_date_added` AS `list_item_date_added`,'0' AS `domainID` from `rayarcca_cloud`.`list_item` union select `rayarcca_music`.`list_item`.`list_id` AS `list_id`,`rayarcca_music`.`list_item`.`list_type_id` AS `list_type_id`,`rayarcca_music`.`list_item`.`item_id` AS `item_id`,`rayarcca_music`.`list_item`.`list_item_date_added` AS `list_item_date_added`,'5' AS `domainID` from `rayarcca_music`.`list_item` union select `rayarcca_yeidol`.`list_item`.`list_id` AS `list_id`,`rayarcca_yeidol`.`list_item`.`list_type_id` AS `list_type_id`,`rayarcca_yeidol`.`list_item`.`item_id` AS `item_id`,`rayarcca_yeidol`.`list_item`.`list_item_date_added` AS `list_item_date_added`,'6' AS `domainID` from `rayarcca_yeidol`.`list_item` union select `rayarcca_nashelsevent`.`list_item`.`list_id` AS `list_id`,`rayarcca_nashelsevent`.`list_item`.`list_type_id` AS `list_type_id`,`rayarcca_nashelsevent`.`list_item`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`list_item`.`list_item_date_added` AS `list_item_date_added`,'7' AS `domainID` from `rayarcca_nashelsevent`.`list_item` union select `rayarcca_yai`.`list_item`.`list_id` AS `list_id`,`rayarcca_yai`.`list_item`.`list_type_id` AS `list_type_id`,`rayarcca_yai`.`list_item`.`item_id` AS `item_id`,`rayarcca_yai`.`list_item`.`list_item_date_added` AS `list_item_date_added`,'12' AS `domainID` from `rayarcca_yai`.`list_item`;

-- --------------------------------------------------------

--
-- Structure for view `member_item_access`
--
DROP TABLE IF EXISTS `member_item_access`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `member_item_access` AS select `rayarcca_music`.`member_item_access`.`access_id` AS `access_id`,`rayarcca_music`.`member_item_access`.`item_id` AS `item_id`,`rayarcca_music`.`member_item_access`.`access_type` AS `access_type`,`rayarcca_music`.`member_item_access`.`access_time` AS `access_time`,`rayarcca_music`.`member_item_access`.`session_id` AS `session_id`,`rayarcca_music`.`member_item_access`.`ip_address` AS `ip_address`,`rayarcca_music`.`member_item_access`.`session_member_id` AS `session_member_id`,5 AS `domain_id` from `rayarcca_music`.`member_item_access` union select `rayarcca_yeidol`.`member_item_access`.`access_id` AS `access_id`,`rayarcca_yeidol`.`member_item_access`.`item_id` AS `item_id`,`rayarcca_yeidol`.`member_item_access`.`access_type` AS `access_type`,`rayarcca_yeidol`.`member_item_access`.`access_time` AS `access_time`,`rayarcca_yeidol`.`member_item_access`.`session_id` AS `session_id`,`rayarcca_yeidol`.`member_item_access`.`ip_address` AS `ip_address`,`rayarcca_yeidol`.`member_item_access`.`session_member_id` AS `session_member_id`,6 AS `domain_id` from `rayarcca_yeidol`.`member_item_access` union select `rayarcca_nashelsevent`.`member_item_access`.`access_id` AS `access_id`,`rayarcca_nashelsevent`.`member_item_access`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`member_item_access`.`access_type` AS `access_type`,`rayarcca_nashelsevent`.`member_item_access`.`access_time` AS `access_time`,`rayarcca_nashelsevent`.`member_item_access`.`session_id` AS `session_id`,`rayarcca_nashelsevent`.`member_item_access`.`ip_address` AS `ip_address`,`rayarcca_nashelsevent`.`member_item_access`.`session_member_id` AS `session_member_id`,7 AS `domain_id` from `rayarcca_nashelsevent`.`member_item_access` union select `rayarcca_nashelsevent`.`member_item_access`.`access_id` AS `access_id`,`rayarcca_nashelsevent`.`member_item_access`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`member_item_access`.`access_type` AS `access_type`,`rayarcca_nashelsevent`.`member_item_access`.`access_time` AS `access_time`,`rayarcca_nashelsevent`.`member_item_access`.`session_id` AS `session_id`,`rayarcca_nashelsevent`.`member_item_access`.`ip_address` AS `ip_address`,`rayarcca_nashelsevent`.`member_item_access`.`session_member_id` AS `session_member_id`,7 AS `domain_id` from `rayarcca_nashelsevent`.`member_item_access`;

-- --------------------------------------------------------

--
-- Structure for view `member_item_like`
--
DROP TABLE IF EXISTS `member_item_like`;

CREATE ALGORITHM=UNDEFINED DEFINER=`rayarcca_root`@`localhost` SQL SECURITY DEFINER VIEW `member_item_like` AS select `rayarcca_music`.`member_item_like`.`like_id` AS `like_id`,`rayarcca_music`.`member_item_like`.`item_id` AS `item_id`,`rayarcca_music`.`member_item_like`.`like_value` AS `like_value`,`rayarcca_music`.`member_item_like`.`like_time` AS `like_time`,`rayarcca_music`.`member_item_like`.`session_id` AS `session_id`,`rayarcca_music`.`member_item_like`.`ip_address` AS `ip_address`,`rayarcca_music`.`member_item_like`.`session_member_id` AS `session_member_id`,5 AS `domain_id` from `rayarcca_music`.`member_item_like` union select `rayarcca_yeidol`.`member_item_like`.`like_id` AS `like_id`,`rayarcca_yeidol`.`member_item_like`.`item_id` AS `item_id`,`rayarcca_yeidol`.`member_item_like`.`like_value` AS `like_value`,`rayarcca_yeidol`.`member_item_like`.`like_time` AS `like_time`,`rayarcca_yeidol`.`member_item_like`.`session_id` AS `session_id`,`rayarcca_yeidol`.`member_item_like`.`ip_address` AS `ip_address`,`rayarcca_yeidol`.`member_item_like`.`session_member_id` AS `session_member_id`,6 AS `domain_id` from `rayarcca_yeidol`.`member_item_like` union select `rayarcca_nashelsevent`.`member_item_like`.`like_id` AS `like_id`,`rayarcca_nashelsevent`.`member_item_like`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`member_item_like`.`like_value` AS `like_value`,`rayarcca_nashelsevent`.`member_item_like`.`like_time` AS `like_time`,`rayarcca_nashelsevent`.`member_item_like`.`session_id` AS `session_id`,`rayarcca_nashelsevent`.`member_item_like`.`ip_address` AS `ip_address`,`rayarcca_nashelsevent`.`member_item_like`.`session_member_id` AS `session_member_id`,7 AS `domain_id` from `rayarcca_nashelsevent`.`member_item_like` union select `rayarcca_nashelsevent`.`member_item_like`.`like_id` AS `like_id`,`rayarcca_nashelsevent`.`member_item_like`.`item_id` AS `item_id`,`rayarcca_nashelsevent`.`member_item_like`.`like_value` AS `like_value`,`rayarcca_nashelsevent`.`member_item_like`.`like_time` AS `like_time`,`rayarcca_nashelsevent`.`member_item_like`.`session_id` AS `session_id`,`rayarcca_nashelsevent`.`member_item_like`.`ip_address` AS `ip_address`,`rayarcca_nashelsevent`.`member_item_like`.`session_member_id` AS `session_member_id`,7 AS `domain_id` from `rayarcca_nashelsevent`.`member_item_like`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_session`
--
ALTER TABLE `access_session`
  ADD CONSTRAINT `access_session_ibfk_1` FOREIGN KEY (`access_id`) REFERENCES `access` (`access_id`);

--
-- Constraints for table `client2_location`
--
ALTER TABLE `client2_location`
  ADD CONSTRAINT `client2_location_ibfk_1` FOREIGN KEY (`mc_id`) REFERENCES `market_channel` (`mc_id`),
  ADD CONSTRAINT `client2_location_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client1_contact` (`client_id`);

--
-- Constraints for table `client2_location_temp`
--
ALTER TABLE `client2_location_temp`
  ADD CONSTRAINT `client2_location_temp_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client1_contact_temp` (`client_id`);

--
-- Constraints for table `client_member`
--
ALTER TABLE `client_member`
  ADD CONSTRAINT `client_member_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client1_contact` (`client_id`),
  ADD CONSTRAINT `client_member_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`);

--
-- Constraints for table `cross_domain_detail_statistics`
--
ALTER TABLE `cross_domain_detail_statistics`
  ADD CONSTRAINT `cross_domain_detail_statistics_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `cross_domain_like_count_statistics`
--
ALTER TABLE `cross_domain_like_count_statistics`
  ADD CONSTRAINT `cross_domain_like_count_statistics_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `cross_domain_rolling_item_count_statistics`
--
ALTER TABLE `cross_domain_rolling_item_count_statistics`
  ADD CONSTRAINT `cross_domain_rolling_item_count_statistics_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `cross_domain_rolling_max_like_item_statistics`
--
ALTER TABLE `cross_domain_rolling_max_like_item_statistics`
  ADD CONSTRAINT `cross_domain_rolling_max_like_item_statistics_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `cross_domain_rolling_max_view_item_statistics`
--
ALTER TABLE `cross_domain_rolling_max_view_item_statistics`
  ADD CONSTRAINT `cross_domain_rolling_max_view_item_statistics_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `cross_domain_view_count_statistics`
--
ALTER TABLE `cross_domain_view_count_statistics`
  ADD CONSTRAINT `cross_domain_view_count_statistics_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `domain`
--
ALTER TABLE `domain`
  ADD CONSTRAINT `domain_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client1_contact` (`client_id`),
  ADD CONSTRAINT `domain_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`plan_id`),
  ADD CONSTRAINT `domain_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`plan_id`);

--
-- Constraints for table `domain_account`
--
ALTER TABLE `domain_account`
  ADD CONSTRAINT `domain_account_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`),
  ADD CONSTRAINT `domain_account_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`);

--
-- Constraints for table `domain_member`
--
ALTER TABLE `domain_member`
  ADD CONSTRAINT `domain_member_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`),
  ADD CONSTRAINT `domain_member_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`);

--
-- Constraints for table `domain_member_temp`
--
ALTER TABLE `domain_member_temp`
  ADD CONSTRAINT `domain_member_temp_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`),
  ADD CONSTRAINT `domain_member_temp_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member_temp` (`member_id`);

--
-- Constraints for table `domain_profile`
--
ALTER TABLE `domain_profile`
  ADD CONSTRAINT `domain_profile_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`profile_id`);

--
-- Constraints for table `eblast_master2`
--
ALTER TABLE `eblast_master2`
  ADD CONSTRAINT `eblast_master2_ibfk_1` FOREIGN KEY (`eblast_id`) REFERENCES `eblast_master` (`eblast_id`);

--
-- Constraints for table `item_featured_stack`
--
ALTER TABLE `item_featured_stack`
  ADD CONSTRAINT `item_featured_stack_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `item_premium`
--
ALTER TABLE `item_premium`
  ADD CONSTRAINT `item_premium_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`domain_id`);

--
-- Constraints for table `job_runtime_history`
--
ALTER TABLE `job_runtime_history`
  ADD CONSTRAINT `job_runtime_history_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`);

--
-- Constraints for table `member2`
--
ALTER TABLE `member2`
  ADD CONSTRAINT `member2_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`),
  ADD CONSTRAINT `member2_ibfk_2` FOREIGN KEY (`mc_id`) REFERENCES `market_channel` (`mc_id`);

--
-- Constraints for table `member3`
--
ALTER TABLE `member3`
  ADD CONSTRAINT `member3_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`);

--
-- Constraints for table `member3_temp`
--
ALTER TABLE `member3_temp`
  ADD CONSTRAINT `member3_temp_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member_temp` (`member_id`);

--
-- Constraints for table `member_social_login`
--
ALTER TABLE `member_social_login`
  ADD CONSTRAINT `member_social_login_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`);

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`);

--
-- Constraints for table `profile_legacy`
--
ALTER TABLE `profile_legacy`
  ADD CONSTRAINT `profile_legacy_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member_legacy` (`member_id`);

--
-- Constraints for table `prospect`
--
ALTER TABLE `prospect`
  ADD CONSTRAINT `prospect_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `prospect_ibfk_2` FOREIGN KEY (`mc_id`) REFERENCES `market_channel` (`mc_id`),
  ADD CONSTRAINT `prospect_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`plan_id`);

--
-- Constraints for table `prospect_service`
--
ALTER TABLE `prospect_service`
  ADD CONSTRAINT `prospect_service_ibfk_1` FOREIGN KEY (`prospect_id`) REFERENCES `prospect` (`prospect_id`),
  ADD CONSTRAINT `prospect_service_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`);

--
-- Constraints for table `role_child_link`
--
ALTER TABLE `role_child_link`
  ADD CONSTRAINT `role_child_link_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `role_family`
--
ALTER TABLE `role_family`
  ADD CONSTRAINT `role_family_ibfk_1` FOREIGN KEY (`role_parent_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `role_family_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `subscriber_access`
--
ALTER TABLE `subscriber_access`
  ADD CONSTRAINT `subscriber_access_ibfk_1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`subscriber_id`),
  ADD CONSTRAINT `subscriber_access_ibfk_2` FOREIGN KEY (`access_id`) REFERENCES `access` (`access_id`);

--
-- Constraints for table `venue`
--
ALTER TABLE `venue`
  ADD CONSTRAINT `venue_ibfk_1` FOREIGN KEY (`venue_type_id`) REFERENCES `venue_type` (`venue_type_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
