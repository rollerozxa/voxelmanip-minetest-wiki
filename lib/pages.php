<?php

function checkPageExistance($pagename) {
	return result("SELECT COUNT(*) FROM wikipages WHERE BINARY title = ?", [$pagename]) == 1;
}
