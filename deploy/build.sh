echo "Step 1/2 : Building image..."

while read -r line
do
  export $line
done < ".variables"

IMAGE=$IMAGE_NAME
TAG=$IMAGE_TAG

docker rm --force $CONTAINER_NAME

docker rmi $IMAGE:$TAG

docker build --quiet -t $IMAGE:$TAG .