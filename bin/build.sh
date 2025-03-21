#!/bin/bash
source ~/.bashrc

SOURCE_PLUGIN_DIR="F:\DreamEncodeAssets\WP Plugins\de-wp-plugin-boilerplate"
HOME_DIR="C:\Users\david"
TEMPLATES_DIR="$SOURCE_PLUGIN_DIR\templates"

CURRENT_DIR=$(pwd)

 # Get plugin name.
read -e -p "Plugin name: " -i "Dream Encode - Test" PLUGIN_NAME
if [ -z "$PLUGIN_NAME" ]
then
	echo "No plugin name supplied. Exiting!"
	exit 1
fi

# Namespace.
read -e -p "Plugin namespace(double backslashes): " -i "Dream_Encode\\\\Test" PLUGIN_NAMESPACE
if [ -z "$PLUGIN_NAMESPACE" ]
then
	echo "No plugin namespace supplied. Exiting!"
	exit 1
fi

# Plugin description.
read -e -p "Plugin description: " -i "A custom plugin..." PLUGIN_DESCRIPTION
if [ -z "$PLUGIN_DESCRIPTION" ]
then
	echo "No plugin description supplied. Exiting!"
	exit 1
fi

# Plugin slug.
PLUGIN_SLUG=$(slugify "$PLUGIN_NAME")

# Functions.
PLUGIN_FUNC_PREFIX=$(wp_replace_dashes_with_underscores "$PLUGIN_SLUG")

# Classes.
PLUGIN_CLASS_PREFIX=$(echo "$PLUGIN_FUNC_PREFIX" | sed -e 's/\(^\|_\)\([a-z]\)/\1\u\2/g')

# Defines.
PLUGIN_DEFINE_PREFIX=${PLUGIN_FUNC_PREFIX^^}

# Short Defines.
PLUGIN_SHORT_DEFINE_PREFIX=$(abbrevify "$PLUGIN_DEFINE_PREFIX")

# Abbreviation.
PLUGIN_ABBR=$(abbrevify "$PLUGIN_FUNC_PREFIX")

# Hook prefix.
PLUGIN_HOOK_PREFIX=$(hook_prefixify "$PLUGIN_NAMESPACE")

# Hook prefix.
PLUGIN_NAMESPACE_DOUBLE_SLASHED=$(escape_backslashes "$PLUGIN_NAMESPACE")

echo "Slug: $PLUGIN_SLUG"
echo "Description: $PLUGIN_DESCRIPTION"
echo "Functions: $PLUGIN_FUNC_PREFIX"
echo "Classes: $PLUGIN_CLASS_PREFIX"
echo "Defines: $PLUGIN_DEFINE_PREFIX"
echo "Short Defines: $PLUGIN_SHORT_DEFINE_PREFIX"
echo "Hook Prefix: $PLUGIN_HOOK_PREFIX"
echo "Abbreviation: $PLUGIN_ABBR"
echo "Namespace: $(echo "$PLUGIN_NAMESPACE")"
echo "Namespace (Escaped): $(echo "$PLUGIN_NAMESPACE_DOUBLE_SLASHED")"

# Confirm plugin name.
confirm "Confirm: You will be creating a new WP plugin named $PLUGIN_NAME with the info above. Continue?"
if ([ $? == 1 ])
then
	echo "Exiting."
	exit 1
fi

ROBOCOPY_SOURCE_DIR="$CURRENT_DIR$PLUGIN_SLUG"
ROBOCOPY_EXTRA_EXCLUDE_STRING=""

# Include defaults.
INCLUDE_ADMIN_SETTINGS_PAGE=false
INCLUDE_LIST_TABLE=false
INCLUDE_REST_API=false
INCLUDE_UPGRADER=false
INCLUDE_LOGGER=false
INCLUDE_CLI_COMMANDS=false
INCLUDE_ACTION_SCHEDULER=false

# List Table.
confirm "Include Admin Settings Page?"
if ([ $? == 1 ])
then
	INCLUDE_ADMIN_SETTINGS_PAGE=false
else
	INCLUDE_ADMIN_SETTINGS_PAGE=true
fi

# List Table.
confirm "Include Admin List Table?"
if ([ $? == 1 ])
then
	ROBOCOPY_EXTRA_EXCLUDE_STRING=" //XF $SOURCE_PLUGIN_DIR\plugin-slug\includes\list-table"
else
	INCLUDE_LIST_TABLE=true
fi

# REST API.
confirm "Include REST API?"
if ([ $? == 1 ])
then
	ROBOCOPY_EXTRA_EXCLUDE_STRING=" //XF $SOURCE_PLUGIN_DIR\plugin-slug\includes\rest-api"
else
	INCLUDE_REST_API=true

	# REST API namespace.
	read -e -p "Plugin REST API Namespace (e.g. dream-encode): " PLUGIN_REST_API_NAMESPACE
	if [ -z "$PLUGIN_REST_API_NAMESPACE" ]
	then
		echo "No plugin REST API namespace supplied. Exiting!"
		exit 1
	fi
fi

# Upgrader.
confirm "Include Upgrader?"
if ([ $? == 1 ])
then
	ROBOCOPY_EXTRA_EXCLUDE_STRING=" //XF $SOURCE_PLUGIN_DIR\plugin-slug\includes\upgrade"
else
	INCLUDE_UPGRADER=true

	# Require these dependencies.
	INCLUDE_LOGGER=true
	INCLUDE_ACTION_SCHEDULER=true
fi

# Logger.
if [ ! "$INCLUDE_LOGGER" = true ]
then
	confirm "Include logger?"
	if ([ $? == 1 ])
	then
		ROBOCOPY_EXTRA_EXCLUDE_STRING="$ROBOCOPY_EXTRA_EXCLUDE_STRING //XF $SOURCE_PLUGIN_DIR\plugin-slug\includes\log"
	else
		INCLUDE_LOGGER=true
	fi
fi

# CLI Commands.
confirm "Include CLI Commands?"
if ([ $? == 1 ])
then
	ROBOCOPY_EXTRA_EXCLUDE_STRING="$ROBOCOPY_EXTRA_EXCLUDE_STRING //XF $SOURCE_PLUGIN_DIR\plugin-slug\includes\cli"
else
	INCLUDE_CLI_COMMANDS=true

	# CLI commmands namespace.
	read -e -p "Plugin CLI Command Namespace (e.g. $PLUGIN_ABBR): " -i "$PLUGIN_ABBR" PLUGIN_CLI_COMMANDS_NAMESPACE
	if [ -z "$PLUGIN_CLI_COMMANDS_NAMESPACE" ]
	then
		echo "No plugin CLI commands namespace supplied. Exiting!"
		exit 1
	fi
fi

# Action Scheduler.
if [ ! "$INCLUDE_ACTION_SCHEDULER" = true ]
then
	confirm "Include Action Scheduler?"
	if ([ $? == 1 ])
	then
		INCLUDE_ACTION_SCHEDULER=false
	else
		INCLUDE_ACTION_SCHEDULER=true
	fi
fi

# Copy files.
echo "Copying plugin files..."
# rsync -av --exclude='.git' --exclude='node_modules' --exclude='yarn.lock' --exclude='vendor' --exclude='composer.lock' "$SOURCE_PLUGIN_DIR/plugin-slug/" "$CURRENT_DIR/$PLUGIN_SLUG"
robocopy "$SOURCE_PLUGIN_DIR\plugin-slug " "$CURRENT_DIR/$PLUGIN_SLUG " //MIR //NFL //NDL //NJH //NJS //NS //NP //NC //XD "$SOURCE_PLUGIN_DIR\plugin-slug\.git" //XD "$SOURCE_PLUGIN_DIR\plugin-slug\node_modules" //XD "$SOURCE_PLUGIN_DIR\plugin-slug\node_modules" //XF "$SOURCE_PLUGIN_DIR\plugin-slug\yarn.lock" //XF "$SOURCE_PLUGIN_DIR\plugin-slug\composer.lock"$ROBOCOPY_EXTRA_EXCLUDE_STRING > /dev/null

# Move over to the newly created directory.
cd "$CURRENT_DIR/$PLUGIN_SLUG"

# Rename all files with slug.
echo "Renaming plugin files..."
for file in $(find . -name "*plugin-slug*")
do
  mv $file $(echo "$file" | sed -r "s|plugin-slug|$PLUGIN_SLUG|g")
done

# Dependencies
echo "Processing dependencies..."

# Admin settings page.
if [ "$INCLUDE_ADMIN_SETTINGS_PAGE" = true ]
then
	echo "Including admin settings page..."

	replace_string_with_template 'PLUGIN_ADMIN_MENU_INIT;' "$TEMPLATES_DIR\PLUGIN_ADMIN_MENU_INIT.tpl" admin/class-$PLUGIN_SLUG-admin.php
else
	echo "Skipping admin settings page..."

	replace_string_with_template "PLUGIN_ADMIN_MENU_INIT;" "" "admin/class-$PLUGIN_SLUG-admin.php"
fi

# List Table.
if [ "$INCLUDE_LIST_TABLE" = true ]
then
	echo "Including list table..."

	replace_string_with_template 'PLUGIN_LIST_TABLE_INCLUDE;' "$TEMPLATES_DIR\PLUGIN_LIST_TABLE_INCLUDE.tpl" admin/class-$PLUGIN_SLUG-admin.php
else
	echo "Skipping list table..."

	replace_string_with_template "PLUGIN_LIST_TABLE_INCLUDE;" "" "admin/class-$PLUGIN_SLUG-admin.php"
fi

# Upgrader.
if [ "$INCLUDE_UPGRADER" = true ]
then
	echo "Including upgrader..."

	replace_string_with_template 'PLUGIN_UPGRADER_INCLUDE;' "$TEMPLATES_DIR\PLUGIN_UPGRADER_INCLUDE.tpl" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_UPGRADER_INIT;' "$TEMPLATES_DIR\PLUGIN_UPGRADER_INIT.tpl" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_UPGRADER_PUBLIC_ACTIONS;' "$TEMPLATES_DIR\PLUGIN_UPGRADER_PUBLIC_ACTIONS.tpl" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_UPGRADER_PUBLIC_METHODS;' "$TEMPLATES_DIR\PLUGIN_UPGRADER_PUBLIC_METHODS.tpl" public/class-$PLUGIN_SLUG-public.php
else
	echo "Skipping upgrader..."

	replace_string_with_template 'PLUGIN_UPGRADER_INCLUDE;' "" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_UPGRADER_INIT;' "" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_UPGRADER_PUBLIC_ACTIONS;' "" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_UPGRADER_PUBLIC_METHODS;' "" public/class-$PLUGIN_SLUG-public.php
fi

# Logger.
if [ "$INCLUDE_LOGGER" = true ]
then
	echo "Including logger..."

	replace_string_with_template 'PLUGIN_LOGGER_INCLUDE;' "$TEMPLATES_DIR\PLUGIN_LOGGER_INCLUDE.tpl" includes/class-$PLUGIN_SLUG.php
else
	echo "Skipping logger..."

	replace_string_with_template "PLUGIN_LOGGER_INCLUDE;" "" "includes/class-$PLUGIN_SLUG.php"
fi

# REST API.
if [ "$INCLUDE_REST_API" = true ]
then
	echo "Including REST API..."

	replace_string_with_template 'PLUGIN_REST_API_INCLUDE;' "$TEMPLATES_DIR\PLUGIN_REST_API_INCLUDE.tpl" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_REST_API_ACTIONS;' "$TEMPLATES_DIR\PLUGIN_REST_API_ACTIONS.tpl" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_REST_API_PUBLIC_METHODS;' "$TEMPLATES_DIR\PLUGIN_REST_API_PUBLIC_METHODS.tpl" public/class-$PLUGIN_SLUG-public.php
else
	echo "Skipping REST API..."

	replace_string_with_template "PLUGIN_REST_API_INCLUDE;" "" "includes/class-$PLUGIN_SLUG.php"
	replace_string_with_template "PLUGIN_REST_API_ACTIONS;" "" "includes/class-$PLUGIN_SLUG.php"
	replace_string_with_template 'PLUGIN_REST_API_PUBLIC_METHODS;' "" public/class-$PLUGIN_SLUG-public.php
fi

# CLI Commands.
if [ "$INCLUDE_CLI_COMMANDS" = true ]
then
	echo "Including CLI commands..."

	replace_string_with_template 'PLUGIN_CLI_COMMANDS_INCLUDE;' "$TEMPLATES_DIR\PLUGIN_CLI_COMMANDS_INCLUDE.tpl" includes/class-$PLUGIN_SLUG.php
	replace_string_with_template 'PLUGIN_CLI_COMMANDS_INIT;' "$TEMPLATES_DIR\PLUGIN_CLI_COMMANDS_INIT.tpl" includes/class-$PLUGIN_SLUG.php
else
	echo "Skipping CLI commands..."

	replace_string_with_template "PLUGIN_CLI_COMMANDS_INCLUDE;" "" "includes/class-$PLUGIN_SLUG.php"
	replace_string_with_template "PLUGIN_CLI_COMMANDS_INIT;" "" "includes/class-$PLUGIN_SLUG.php"
fi

# Action Scheduler.
if [ "$INCLUDE_ACTION_SCHEDULER" = true ]
then
	echo "Including Action Scheduler..."

	replace_string_with_template "PLUGIN_ACTION_SCHEDULER_INCLUDE;" "$TEMPLATES_DIR\PLUGIN_ACTION_SCHEDULER_INCLUDE.tpl" "includes/class-$PLUGIN_SLUG.php"
else
	echo "Skipping Action Scheduler..."

	replace_string_with_template "PLUGIN_ACTION_SCHEDULER_INCLUDE;" '' "includes/class-$PLUGIN_SLUG.php"
fi

# Start string replacements.
echo "String replacements..."
echo "Slug..."
grep "PLUGIN_SLUG" . -lr | xargs sed -i "s/PLUGIN_SLUG/$PLUGIN_SLUG/g"
echo "Description..."
grep "PLUGIN_DESCRIPTION" . -lr | xargs sed -i "s/PLUGIN_DESCRIPTION/$PLUGIN_DESCRIPTION/g"
echo "Function Prefix..."
grep "PLUGIN_FUNC_PREFIX" . -lr | xargs sed -i "s/PLUGIN_FUNC_PREFIX/$PLUGIN_FUNC_PREFIX/g"
echo "Class Prefix..."
grep "PLUGIN_CLASS_PREFIX" . -lr | xargs sed -i "s/PLUGIN_CLASS_PREFIX/$PLUGIN_CLASS_PREFIX/g"
echo "Define Prefix..."
grep "PLUGIN_DEFINE_PREFIX" . -lr | xargs sed -i "s/PLUGIN_DEFINE_PREFIX/$PLUGIN_DEFINE_PREFIX/g"
echo "Short Define Prefix..."
grep "PLUGIN_SHORT_DEFINE_PREFIX" . -lr | xargs sed -i "s/PLUGIN_SHORT_DEFINE_PREFIX/$PLUGIN_SHORT_DEFINE_PREFIX/g"
echo "Hook Prefix..."
grep "PLUGIN_HOOK_PREFIX" . -lr | xargs sed -i "s|PLUGIN_HOOK_PREFIX|$PLUGIN_HOOK_PREFIX|g"
echo "Abbreviation..."
grep "PLUGIN_ABBR" . -lr | xargs sed -i "s/PLUGIN_ABBR/$PLUGIN_ABBR/g"

if [ "$INCLUDE_REST_API" = true ]
	then
	echo "REST API Namespace..."
	grep "PLUGIN_REST_API_NAMESPACE" . -lr | xargs sed -i "s/PLUGIN_REST_API_NAMESPACE/$PLUGIN_REST_API_NAMESPACE/g"
fi

if [ "$INCLUDE_CLI_COMMANDS" = true ]
	then
	echo "CLI Commands..."
	# Has backslashes, so need to escape these.
	ESCAPED_PLUGIN_CLI_COMMANDS_NAMESPACE=$(escape_backslashes "$PLUGIN_CLI_COMMANDS_NAMESPACE")
	grep "PLUGIN_CLI_COMMANDS_NAMESPACE" . -lr | xargs sed -i "s/PLUGIN_CLI_COMMANDS_NAMESPACE/$ESCAPED_PLUGIN_CLI_COMMANDS_NAMESPACE/g"
fi

# In the REST API files, some namespaces are double slashed.  Replace these first to not be capture by main namespace replacements.
echo "Double Slashed Namespace..."
ESCAPED_PLUGIN_NAMESPACE_DOUBLE_SLASHED=$(escape_backslashes "$PLUGIN_NAMESPACE_DOUBLE_SLASHED")
grep "PLUGIN_NAMESPACE_DOUBLE_SLASHED" . -lr | xargs sed -i "s/PLUGIN_NAMESPACE_DOUBLE_SLASHED/$ESCAPED_PLUGIN_NAMESPACE_DOUBLE_SLASHED/g"

# Namespace requires some special handling due to the backslashes.
echo "Escaped Namespace..."
ESCAPED_PLUGIN_NAMESPACE=$(escape_backslashes "$PLUGIN_NAMESPACE")
grep "PLUGIN_NAMESPACE" . -lr | xargs sed -i "s/PLUGIN_NAMESPACE/$ESCAPED_PLUGIN_NAMESPACE/g"

# Replace these last so they don't interfere with namespace replacements.
echo "Name..."
grep "PLUGIN_NAME" . -lr | xargs sed -i "s/PLUGIN_NAME/$PLUGIN_NAME/g"
echo "String replacements complete."

# Bash alias.
CLIENT_NAME=$(wp_plugin_get_client)

case "$CLIENT_NAME" in
	"max-marine")
		BASH_ALIASES_FILE="$HOME_DIR\.bash_includes\maxmarine.bashrc"

		ALIAS_STRING="alias $PLUGIN_ABBR='cd /f/MaxMarineAssets/Code/wp-content/plugins/$PLUGIN_SLUG'";;

	"squadron-posters")
		BASH_ALIASES_FILE="$HOME_DIR\.bash_includes\squadronposters.bashrc"

		ALIAS_STRING="alias $PLUGIN_ABBR='cd /f/SquadronPostersAssets/Code/wp-content/plugins/$PLUGIN_SLUG'";;
esac

if [ ! -z "$BASH_ALIASES_FILE" ]
then
	echo "$ALIAS_STRING" >> "$BASH_ALIASES_FILE"
	echo "Plugin alias '$ALIAS_NAME' added to $BASH_ALIASES_FILE"
fi

source ~/.bashrc

# GitHub Repo.
confirm "Do you wish to create a GitHub repo for this plugin?"
if ([ $? == 1 ])
then
	# Update the repo URL in all files.
	grep "GH_REPO_URL" . -lr | xargs sed -i "s/GH_REPO_URL/dream-encode\/$PLUGIN_SLUG/g"
else
	# Initialize a git repo and commit the initial state.
	echo "Initing repo..."
	git init
	git add .
	gc "Initial plugin files"

	# GitHub repo name.
	read -p "Repo slug: " -i "$PLUGIN_SLUG" -e GH_REPO_SLUG
	if [ -z "$GH_REPO_SLUG" ]
	then
		echo "No GitHub repo name supplied. Exiting!"
		exit 1
	fi

	# Public or private repo.
	PUBLIC_PRIVATE_REPO=" --private"

	confirm "Is this repo private?"
	if ([ $? == 1 ])
	then
		PUBLIC_PRIVATE_REPO=" --public"
		echo "GitHub repo will be public."
	else
		echo "GitHub repo will be private."
	fi

	# Create the GH repo.
	echo "Creating GitHub repo..."
	gh repo create "$GH_REPO_SLUG" --source=. $PUBLIC_PRIVATE_REPO

	# Get the full repo URL.
	GH_REPO_URL=$(gh repo view --json nameWithOwner -q ".nameWithOwner")

	# Update the repo URL in all files.
	grep "GH_REPO_URL" . -lr | xargs sed -i "s|GH_REPO_URL|$GH_REPO_URL|g"

	git add .
	gc "Initial commit"

	echo "Pushing to GitHub repo..."
	git remote add origin git@github.com:dream-encode/$GH_REPO_SLUG

	gh repo set-default dream-encode/$GH_REPO_SLUG

	echo "Adding dependencies..."

	if [ "$INCLUDE_ACTION_SCHEDULER" = true ]
	then
		echo "Adding Action Scheduler as Git subtree..."
		git remote add -f subtree-action-scheduler https://github.com/woocommerce/action-scheduler.git

		git subtree add --prefix libraries/action-scheduler subtree-action-scheduler trunk --squash
	fi

	git push -u origin main

	cd "$CURRENT_DIR/$PLUGIN_SLUG"
fi

# Install third-party dependencies.
confirm "Install third-party dependencies now?"
if ([ $? == 1 ])
then
	echo "Plugin successfully created."
	exit
fi

# Install NPM dependencies.
yarn install

# Install composer dependencies.
composer install

echo "Plugin successfully created."