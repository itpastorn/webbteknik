# Uppgifter att godkänna
teacherpage/verify (all active groups)

(Du har x uppgifter att godkänna)

Lista uppgifter per elev

Godkänn - underkänn (gör om/reset)

# Visa framsteg
teacherpage/progress/groupid

(Visa medel/median/topp/botten)

lista alla namn summera deras insatser
--> Klicka på namn = se detaljer (föregående - nästa - round robin)

# Redigera grupp -> Samma formulär som skapar gruppen
Ny knapp - arkivera


[Visa arkiverade grupper] -> radera permanent/återaktivera


# Redigera medlemslistan
teacherpage/members/groupid

Ta bort elev

Lägg till elev via email (ajax efter @ som också visar för- och efternamn)


Livslängd 12 månader -> automatisk arkivering i framtiden (med manuell kontrollfråga)


# Router-categories

Always
 * $baseref

 * TODO - total number of unverified jobs per group


 * GET - module
   * verify/groupid/   For approving/failing reported jobs
   * progress/groupid/ For getting an overview of a groups progress
   * members/groupid/  For adding or deleting group members
   * student/email/    For tracking individual students in complete detail

 * empty(GET) - always
 
 
 
 
 * POST - 'admingroup_form_submitted'
   * $g_group_id           = ''; // For update - not yet implemented
   * $g_group_id_msg       = '';
   * $g_new_group_save_msg = '';

 * POST, 'new_workplace_added'
   * $new_workplace_save_msg = "";

 * POST, 'new_school_school_added'
   * $new_school_save_msg = "";

 * POST, 'new_workplace_added'
   * $new_workplace_save_msg = '';

 * POST, 'new_school_school_added'
   * $new_school_save_msg = '';
   * $new_school          = data_schools::fake(); }



