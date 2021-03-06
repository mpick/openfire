<? class Home{

function get(){
setcookie("user[lastPage]", $_SERVER['REQUEST_URI'], time()+60*60*24*30, "/","openfi.re");

	global $dbh;
	global $user;
	global $embedly;


$recentprojects = array();
$featuredprojects = array();
$featuredgoals = array();
$recentupdates = array();
$categories = array();

	$sth = $dbh->prepare("SELECT id FROM projects where status = 'published' order by dateAdded desc limit 5");
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $p){
	$recentprojects[] = new Project($p['id']);
}


$sth = $dbh->prepare("SELECT projectID FROM featuredProjects where isCurrent = '1' order by dateAdded desc limit 5");
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $p){
	$featuredprojects[] = new Project($p['projectID']);
}

$sth = $dbh->prepare("SELECT goalID, description FROM featuredGoals where isCurrent = '1' order by dateAdded desc limit 5");
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $p){
	$thisgoal = new Goal($p['goalID']);
	$thisgoal->featuredDescription = $p['description'];
	$featuredgoals[] = $thisgoal;
}

$sth = $dbh->prepare("SELECT id FROM updates where deleted = '0' and public='1' order by dateAdded desc limit 5");
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $p){
	$recentupdates[] = new Update($p['id']);
}


	$sth = $dbh->prepare("SELECT id FROM projectCategories where subcategoryOf = '0'");
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $p){
	$categories[] = new projectCategory($p['id']);
}


$template = new Templater();
$template->load('header');
$template->bodyClass = "home";
$template->publish();

$template = new Templater();
$template->load('homePage');
$template->embedly = $embedly;
$template->featuredProjects = $featuredprojects;
$template->featuredGoals = $featuredgoals;

$template->recentProjects = $recentprojects;
$template->recentUpdates = $recentupdates;
$template->categories = $categories;

$template->publish();



$template = new Templater();
$template->load('footer');
$template->publish();

}

} 

?>