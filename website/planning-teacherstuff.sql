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
