import os
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics import confusion_matrix
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LogisticRegression
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier
import pickle
import joblib

# Load the data
df = pd.read_csv('Data.csv')
print(df.head(7))

# Count the number of rows and columns in the dataset
print(df.shape)

# Count the number of empty values (NAN, NaN, na) in each column
print(df.isna().sum())

# Drop the columns with all the missing values
df = df.dropna(axis=1)

# Get the new counts of the number of rows and columns
print(df.shape)

# Get a count of the number of Malignant (M) or Benign (B) cells
print(df['diagnosis'].value_counts())

# Visualize the count
sns.countplot(x='diagnosis', data=df, label='count')
plt.show()

# Look at the data types to see which columns need to be encoded
print(df.dtypes)

# Encode the categorical data values
labelencoder_Y = LabelEncoder()
df['diagnosis'] = labelencoder_Y.fit_transform(df['diagnosis'])

# Create a pair plot
sns.pairplot(df.iloc[:, 1:5], hue='diagnosis')
plt.show()

# Print the first 5 rows of the new data
print(df.head(5))

# Get the correlation of the columns
corr_matrix = df.iloc[:, 1:12].corr()
print(corr_matrix)

# Visualize the correlation
plt.figure(figsize=(10, 10))
sns.heatmap(df.iloc[:, 1:12].corr(), annot=True, fmt='.0%')
plt.show()

# Split the dataset into independent (X) and dependent (Y) datasets
X = df.iloc[:, 2:31].values
Y = df.iloc[:, 1].values

# Split the dataset into 75% training and 25% testing
X_train, X_test, Y_train, Y_test = train_test_split(X, Y, test_size=0.25, random_state=0)

# Scale the data (Feature Scaling)
sc = StandardScaler()
X_train = sc.fit_transform(X_train)
X_test = sc.transform(X_test)

# Create the 'models' directory if it doesn't exist
if not os.path.exists('models'):
    os.makedirs('models')

# Save the scaler for later use
joblib.dump(sc, 'models/scaler.pkl')

# Create a function for the models
def models(X_train, Y_train):
    # Logistic Regression
    log = LogisticRegression(random_state=0)
    log.fit(X_train, Y_train)

    # Decision Tree
    tree = DecisionTreeClassifier(criterion='entropy', random_state=0)
    tree.fit(X_train, Y_train)

    # Random Forest Classifier
    forest = RandomForestClassifier(n_estimators=10, criterion='entropy', random_state=0)
    forest.fit(X_train, Y_train)

    # Print the models' accuracy on the training data
    print('[0]Logistic Regression Training Accuracy:', log.score(X_train, Y_train))
    print('[1]Decision Tree Classifier Training Accuracy:', tree.score(X_train, Y_train))
    print('[2]Random Forest Classifier Training Accuracy:', forest.score(X_train, Y_train))

    return log, tree, forest

# Train models and save them
log_model, tree_model, forest_model = models(X_train, Y_train)

# Save the models to pickle files
with open('models/log_model.pkl', 'wb') as file:
    pickle.dump(log_model, file)
with open('models/tree_model.pkl', 'wb') as file:
    pickle.dump(tree_model, file)
with open('models/forest_model.pkl', 'wb') as file:
    pickle.dump(forest_model, file)

# Test model accuracy on the test data using a confusion matrix
predictions_log = log_model.predict(X_test)
predictions_tree = tree_model.predict(X_test)
predictions_forest = forest_model.predict(X_test)

# Print confusion matrix for each model
print('Confusion Matrix for Logistic Regression')
print(confusion_matrix(Y_test, predictions_log))
print('Confusion Matrix for Decision Tree')
print(confusion_matrix(Y_test, predictions_tree))
print('Confusion Matrix for Random Forest')
print(confusion_matrix(Y_test, predictions_forest))

# Print model accuracy for each model
print('Logistic Regression Test Accuracy:', log_model.score(X_test, Y_test))
print('Decision Tree Test Accuracy:', tree_model.score(X_test, Y_test))
print('Random Forest Test Accuracy:', forest_model.score(X_test, Y_test))
