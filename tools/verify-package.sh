#!/usr/bin/env sh
#
# Builds the package exactly as it would be published, installs it into a throwaway
# project, and uses it the way a consumer would.
#
#   sh tools/verify-package.sh
#
# This is what stops .gitattributes from silently breaking the release: `export-ignore`
# decides what ships, and nothing else in the test suite looks at the published artefact.
# Excluding src/ or schema/ by accident would pass every other check.

set -eu

ROOT=$(cd "$(dirname "$0")/.." && pwd)
WORK=$(mktemp -d)
# Scoped to the temp dir this script created, never a variable that could be empty.
trap 'rm -rf "$WORK"' EXIT INT TERM

PKG="$WORK/package"
APP="$WORK/consumer"
mkdir -p "$PKG" "$APP"

echo "==> Building the dist from git archive"
git -C "$ROOT" archive HEAD | tar -x -C "$PKG"

for required in src composer.json LICENSE bin/edifact schema/message.schema.json; do
    if [ ! -e "$PKG/$required" ]; then
        echo "FAIL: '$required' is missing from the published package" >&2
        exit 1
    fi
done

for excluded in tests tools example docs docu .github phpunit.xml; do
    if [ -e "$PKG/$excluded" ]; then
        echo "FAIL: '$excluded' should not ship — check .gitattributes" >&2
        exit 1
    fi
done

echo "==> Installing it into a throwaway project"
cat > "$APP/composer.json" <<JSON
{
    "name": "verify/consumer",
    "require": { "chemaclass/edifact-parser": "*" },
    "repositories": [
        { "type": "path", "url": "$PKG", "options": { "symlink": false } }
    ],
    "minimum-stability": "dev"
}
JSON

( cd "$APP" && composer install --no-interaction --no-ansi --quiet )

echo "==> Using it as a consumer"
cat > "$APP/use.php" <<'PHP'
<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Validation\MessageRuleSets;
use EdifactParser\Validation\MessageValidator;

$edi = "UNA:+.? 'UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+137:20240101:102'"
     . "NAD+BY+123::9+++Street 1+Berlin++10115+DE'LIN+1++ART1:BP'QTY+21:100'UNT+8+1'UNZ+1+1'";

$message = EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];

assert($message->messageType() === 'ORDERS');
$buyer = $message->segmentByTagAndSubId('NAD', 'BY');
assert($buyer instanceof NADNameAddress);
assert($buyer->city() === 'Berlin');
assert($message->lineItemById(1)?->segmentByTagAndSubId('QTY', '21')?->quantityAsFloat() === 100.0);
assert((new MessageValidator())->validate($message, MessageRuleSets::orders()) === []);
assert(is_array(json_decode($message->toJson(), true)));

// The generated segment classes must be in the package, not just in the repo.
assert(count(SegmentFactory::withDirectorySegments()->registeredTags()) === 134);

// Non-ASCII survival is the headline 7.0 behaviour; verify it from outside the repo.
$umlaut = EdifactParser::createWithDefaultSegments()
    ->parse("UNH+1+ORDERS:D:96A:UN'NAD+BY+++Müller GmbH'UNT+3+1'")
    ->transactionMessages()[0];
assert($umlaut->segmentByTagAndSubId('NAD', 'BY')?->name() === 'Müller GmbH');

echo "library OK\n";
PHP

php -d zend.assertions=1 -d assert.exception=1 "$APP/use.php"

echo "==> Using the CLI as an installed dependency"
printf "%s" "UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'" > "$APP/order.edi"

# The binary resolves the autoloader differently when installed as a dependency than when
# run from the repo, so this path is only exercised here.
"$APP/vendor/bin/edifact" inspect "$APP/order.edi" > /dev/null
"$APP/vendor/bin/edifact" segments --tag=QTY > /dev/null
"$APP/vendor/bin/edifact" validate "$APP/order.edi" --rules=ORDERS > /dev/null
echo "CLI OK"

echo
echo "Package verified: $(du -sh "$PKG" | cut -f1) installed and working."
