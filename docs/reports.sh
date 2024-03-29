#!/bin/sh

now="$(date +'%Y-%m-%d')"

logfile="../logs/laskunautti.$now.log"

echo ""
echo "#"
echo "# Laskunautti activity report for today ($now)"
echo "#"
echo ""

echo "Homepage loads today:"
grep 'GET / \[\]' $logfile | wc -l
echo ""

echo "Sample viewed today:"
grep 'Sample loaded' $logfile | wc -l
echo ""

echo "Previews created today:"
grep 'Preview loaded' $logfile | wc -l
echo ""

echo "Invoices created today:"
grep 'Invoice saved' $logfile | wc -l
echo ""

echo "Invoices viewed today:"
grep 'Invoice viewed' $logfile | wc -l
echo ""

echo "List of Invoices created today:"
grep 'Invoice saved' $logfile | sed 's/^.*Invoice saved: //' | sed 's/ .*$//'
echo ""

yesterday="$(date -d '1 day ago' +'%Y-%m-%d')"

logfile="../logs/laskunautti.$yesterday.log"

echo ""
echo "#"
echo "# Laskunautti activity report for yesterday ($yesterday)"
echo "#"
echo ""

echo "Homepage loads yesterday:"
grep 'GET / \[\]' $logfile | wc -l
echo ""

echo "Sample viewed yesterday:"
grep 'Sample loaded' $logfile | wc -l
echo ""

echo "Previews created yesterday:"
grep 'Preview loaded' $logfile | wc -l
echo ""

echo "Invoices created yesterday:"
grep 'Invoice saved' $logfile | wc -l
echo ""

echo "Invoices viewed yesterday:"
grep 'Invoice viewed' $logfile | wc -l
echo ""

echo "List of Invoices created yesterday:"
grep 'Invoice saved' $logfile | sed 's/^.*Invoice saved: //' | sed 's/ .*$//'
echo ""
