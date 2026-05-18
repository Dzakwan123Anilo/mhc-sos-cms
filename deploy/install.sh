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
composer install"