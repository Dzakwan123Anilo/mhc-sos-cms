echo "Step 3/3 : Installing libraries..."

while read -r line
do
  export $line
done < ".variables"

docker exec -ti $CONTAINER_NAME bash \
-c "cd /home/app && \
chmod -R 777 bootstrap && \
chmod -R 777 storage && \
rm -rf vendor && \
/usr/local/bin/php /usr/bin/composer.phar install"