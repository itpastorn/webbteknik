    -- User total progress
SELECT count(*) AS count, up.status, jl.bookID FROM userprogress AS up
INNER JOIN joblist AS jl USING (joblistID)
WHERE up.email = 'gunther@keryx.se'
GROUP BY jl.bookID, up.status
ORDER BY jl.bookID, FIELD(up.status, 'finished', 'skipped', 'begun')

-- per chapter
SELECT count(*) AS count, up.status, jl.bookID, jl.chapter FROM userprogress AS up
INNER JOIN joblist AS jl USING (joblistID)
WHERE up.email = 'gunther@keryx.se'
-- AND jl.what_to_do = 'video'
GROUP BY jl.bookID, jl.chapter, up.status
ORDER BY jl.bookID, jl.chapter ASC, FIELD(up.status, 'finished', 'skipped', 'begun')

-- Possible number of "jobs"
SELECT count(*) AS total, bookID
FROM joblist
GROUP BY bookID


-- Unapproved per group
SELECT count(*) AS count, up.status, jl.bookID FROM userprogress AS up
INNER JOIN joblist AS jl USING (joblistID)
INNER JOIN belonging_groups AS bg ON (bg.email = up.email)

WHERE bg.groupID = 'c1r89'
  AND up.approved IS NULL
  AND ( up.status = 'finished' OR up.status = 'skipped' )
GROUP BY bg.groupID, up.status

-- Unapproved - show item by student
SELECT jl.*, up.percentage_complete, up.status, up.lastupdate, up.email, users.firstName, users.lastName
FROM joblist AS jl
INNER JOIN userprogress AS up USING (joblistID)
INNER JOIN belonging_groups AS bg ON ( bg.email = up.email)
INNER JOIN teaching_groups AS tg ON ( tg.groupID = bg.groupID)
INNER JOIN users ON (bg.email = users.email)
WHERE tg.email = 'gunther@keryx.se'
  AND up.approved IS NULL
  AND ( up.status = 'finished' OR up.status = 'skipped' )
ORDER BY bg.groupID, users.email, jl.bookID, jl.chapter ASC, jl.joborder ASC



-- ## Preference. What to track "only video"/

-- ## Button: "Godkänn alla" (ev. checkbox + markera alla + med markerade "godkänn"/"underkänn") 




-- ## Users that have come this year - have they done anything, do they belong to a group?
SELECT jl.bookID, users.email, users.`user_since`, MAX(up.lastupdate), acl.bookID, bg.groupID
FROM `users`
LEFT JOIN access_control AS acl ON (users.email = acl.email)
LEFT JOIN userprogress AS up ON (users.email = up.email)
LEFT JOIN joblist AS jl USING (joblistID)
LEFT JOIN belonging_groups AS bg ON (users.email = bg.email)
WHERE users.user_since > NOW() - INTERVAL 6 MONTH AND bg.groupID IS NULL AND users.privileges <  30
GROUP BY users.email, jl.bookID
ORDER BY ISNULL(jl.bookID)DESC, FIELD(jl.bookID, 'ws1', 'wu1')

-- priv = 15 = group
-- priv =  7 = no group - ALWAYS....? Nope, according to
SELECT * FROM users
INNER JOIN belonging_groups USING (email)
WHERE users.privileges <> 15
ORDER BY privileges DESC, user_since

--AND
SELECT * FROM users
LEFT JOIN belonging_groups AS bg USING (email)
WHERE users.privileges = 15
ORDER BY ISNULL(bg.email) DESC, privileges DESC, user_since
-- 2 = p 15 but no group

-- LOOP users LEFT JOIN acl LEFT JOIN belonging_groups LEFT JOIN userprogress count() max(lastupdate) GROUP by email
-- user that has a current book - ignore totally below
-- Every user p <= 15 that joined last year and has not seen or done anything last 3 monts set p=1, privlevel since NOW
   -- p 1 AND privlevel since > user since + INTERVAL 2 MONTH = "inactivated"
   -- email "you have now been inactivated" + reklam för ws 1 + detta är sista mejlet + instruktioner för återaktivering
   -- log to textfile
-- Every user p <= 15 that joined before ws1 and is not in group and p >= 7 AND acl is NULL set ACL wu1
-- Every user p <= 15 > 1 that joined after ws1 and has not done anything at all AND acl is NULL gets an email
-- Every user p <= 15 > 1 that joined after ws1 AND has not access to any book either through group or ACL gets an email

-- Why did not redirect work when no choice at all is available (neither group nor ACL)....?


