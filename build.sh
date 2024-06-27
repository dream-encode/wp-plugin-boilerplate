#!/bin/bash
source ~/.bashrc

SOURCE_PLUGIN_DIR="/f/DreamEncodeAssets/WP Plugins/de-wp-plugin-boilerplate"
CURRENT_DIR=$(pwd)

 # Get plugin name.
read -p "Plugin name: " PLUGIN_NAME
if [ -z "$PLUGIN_NAME" ]
then
	echo "No plugin name supplied. Exiting!"
	exit 1
fi

# Namespace.
read -p "Plugin namespace(double backslashes): " PLUGIN_NAMESPACE
if [ -z "$PLUGIN_NAMESPACE" ]
then
	echo "No plugin namespace supplied. Exiting!"
	exit 1
fi

# Plugin description.
read -p "Plugin description: " PLUGIN_DESCRIPTION
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
PLUGIN_SHORT_DEFINE_PREFIX=${PLUGIN_DEFINE_PREFIX,,}

# Abbreviation.
PLUGIN_ABBR=$(abbrevify "$PLUGIN_FUNC_PREFIX")

# Hook prefix.
PLUGIN_HOOK_PREFIX=$(hook_prefixify "$PLUGIN_NAMESPACE")

# Hook prefix.
PLUGIN_NAMESPACE_DOUBLE_SLASHED=$(echo "$PLUGIN_NAMESPACE" | sed 's/\\/\\\\/g')

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

# Confirm plugin replacements.
confirm "Do the above info look good?"
if ([ $? == 1 ])
then
	echo "Exiting."
	exit 1
fi

# Confirm plugin name.
confirm "Confirm: You will be creating a new WP plugin named $PLUGIN_NAME. Continue?"
if ([ $? == 1 ])
then
	echo "Exiting."
	exit 1
fi

# Copy files.
rsync -av --exclude='.git' --exclude='node_modules' --exclude='yarn.lock' --exclude='vendor' --exclude='composer.lock' "$SOURCE_PLUGIN_DIR/plugin-slug/" "$CURRENT_DIR/$PLUGIN_SLUG"

# Move over to the newly created directory.
cd "$CURRENT_DIR/$PLUGIN_SLUG"

# Rename all files with slug.
for file in $(find .)
do
  mv $file $(echo "$file" | sed -r "s|plugin-slug|$PLUGIN_SLUG|g")
done

# Start string replacements.
grep "PLUGIN_SLUG" . -lr | xargs sed -i "s/PLUGIN_SLUG/$PLUGIN_SLUG/g"
grep "PLUGIN_DESCRIPTION" . -lr | xargs sed -i "s/PLUGIN_DESCRIPTION/$PLUGIN_DESCRIPTION/g"
grep "PLUGIN_FUNC_PREFIX" . -lr | xargs sed -i "s/PLUGIN_FUNC_PREFIX/$PLUGIN_FUNC_PREFIX/g"
grep "PLUGIN_CLASS_PREFIX" . -lr | xargs sed -i "s/PLUGIN_CLASS_PREFIX/$PLUGIN_CLASS_PREFIX/g"
grep "PLUGIN_DEFINE_PREFIX" . -lr | xargs sed -i "s/PLUGIN_DEFINE_PREFIX/$PLUGIN_DEFINE_PREFIX/g"
grep "PLUGIN_SHORT_DEFINE_PREFIX" . -lr | xargs sed -i "s/PLUGIN_SHORT_DEFINE_PREFIX/$PLUGIN_SHORT_DEFINE_PREFIX/g"
grep "PLUGIN_HOOK_PREFIX" . -lr | xargs sed -i "s|PLUGIN_HOOK_PREFIX|$PLUGIN_HOOK_PREFIX|g"
grep "PLUGIN_ABBR" . -lr | xargs sed -i "s/PLUGIN_ABBR/$PLUGIN_ABBR/g"

# In the REST API files, some namespaces are double slashed.  Replace these first to not be capture by main namespace replacements.
ESCAPED_PLUGIN_NAMESPACE_DOUBLE_SLASHED=$(echo "$PLUGIN_NAMESPACE_DOUBLE_SLASHED" | sed 's/\\/\\\\/g')
grep "PLUGIN_NAMESPACE_DOUBLE_SLASHED" . -lr | xargs sed -i "s/PLUGIN_NAMESPACE_DOUBLE_SLASHED/$ESCAPED_PLUGIN_NAMESPACE_DOUBLE_SLASHED/g"

# Namespace requires soem special handling due to the backslashes.
ESCAPED_PLUGIN_NAMESPACE=$(echo "$PLUGIN_NAMESPACE" | sed 's/\\/\\\\/g')
grep "PLUGIN_NAMESPACE" . -lr | xargs sed -i "s/PLUGIN_NAMESPACE/$ESCAPED_PLUGIN_NAMESPACE/g"

# Replace these last so they don't interfere with namespace replacements.
grep "PLUGIN_NAME" . -lr | xargs sed -i "s/PLUGIN_NAME/$PLUGIN_NAME/g"

# GitHub Repo.
confirm "Do you wish to create a GitHub repo for this plugin?"
if ([ $? == 1 ])
then
	# Update the repo URL in all files.
	grep "GH_REPO_URL" . -lr | xargs sed -i "s|GH_REPO_URL|dream-encode/$PLUGIN_SLUG|g"
else
	# Initialize a git repo and commit the inital state.
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
	PUBLIC_PRIVATE_REPO=" --public"

	confirm "Is this repo private?"
	if ([ $? == 1 ])
	then
		PUBLIC_PRIVATE_REPO=" --private"
		echo "GitHub repo will be private."
	fi

	# Create the GH repo.
	gh repo create "$GH_REPO_SLUG" --source=. $PUBLIC_PRIVATE_REPO

	# Get the full repo URL.
	GH_REPO_URL=$(gh repo view --json nameWithOwner -q ".nameWithOwner")

	# Update the repo URL in all files.
	grep "GH_REPO_URL" . -lr | xargs sed -i "s|GH_REPO_URL|$GH_REPO_URL|g"

	git add .
	gc "Initial commit"

	git remote add origin git@github.com:dream-encode/$GH_REPO_SLUG

	gh repo set-default dream-encode/$GH_REPO_SLUG

	git push -u origin main

	git remote add -f subtree-action-scheduler https://github.com/woocommerce/action-scheduler.git

	git subtree add --prefix libraries/action-scheduler subtree-action-scheduler trunk --squash
fi

# Install NPM dependencies.
yarn install

# Install composer dependencies.
composer install