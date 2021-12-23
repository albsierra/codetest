db = db.getSiblingDB('exercises-storage');

db.createCollection('user');

db.user.insertMany([{
    "displayName": "codetest",
    "userName": "codetest",
    "email": "codetest@email.com",
    "password": "$2a$10$FGQqxS47ftCbO1dcRLFOc.MxgMg1UGEUieKjc5xg/8iW/xysY9mSK",
    // password is "c0d3te5t" encrypted with the secret "SecretKey"
    "_class": "com.juezlti.repository.models.User"
}]);
